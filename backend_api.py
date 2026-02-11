from fastapi import FastAPI, HTTPException, Header, Depends
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from datetime import datetime
import secrets
import string
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

app = FastAPI()

# --- 1. IZIN AKSES (CORS) ---
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# --- 2. SECURITY CONFIG ---
API_KEY_SECRET = "TRUSUR_SECRET_KEY_2024"

# --- 3. KONFIGURASI MAILTRAP ---
SMTP_SERVER = "sandbox.smtp.mailtrap.io"
SMTP_PORT = 2525
SMTP_USERNAME = "831e2dc4073246"
SMTP_PASSWORD = "9e4dcc46802ab4"
SENDER_EMAIL = "admin.center@trusur.tech"

def verify_api_key(x_api_key: str = Header(None)):
    if x_api_key != API_KEY_SECRET:
        raise HTTPException(status_code=403, detail="Forbidden: API Key Invalid")
    return x_api_key

# --- 4. DATA STORAGE (TANPA EXPIRED_AT) ---
loggers_db = [
    {
        "logger_id": "LOG-001",
        "logger_name": "Unit DAS Laptop Kamu",
        "user_email": "redninja1sr@gmail.com",
        "activation_code": "None",
        "token": "None",
        "status": "Pending",
        "created_at": None
    }
]

admin_notifications = []

# --- 5. FUNGSI KIRIM EMAIL (VERSI UNLIMITED) ---
def send_verification_email(receiver_email, code, logger_id):
    subject = f"🔐 Kode Verifikasi Akses - {logger_id}"

    # Menghapus teks "60 menit" agar tidak membingungkan user
    html_content = f"""
    <html>
        <body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6;">
            <div style="max-width: 600px; margin: 20px auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div style="background-color: #009688; padding: 25px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 22px; letter-spacing: 1px;">DAS SECURITY CENTER</h1>
                </div>
                <div style="padding: 30px; background-color: #ffffff;">
                    <p style="font-size: 16px;">Halo,</p>
                    <p>Gunakan kode di bawah ini untuk otorisasi akses sistem DAS pada ID <strong>{logger_id}</strong>:</p>
                    <div style="background-color: #f4f7f6; border: 2px dashed #009688; padding: 20px; text-align: center; border-radius: 10px; margin: 25px 0;">
                        <span style="font-size: 32px; font-weight: bold; color: #009688; letter-spacing: 8px;">{code}</span>
                    </div>
                    <p style="font-size: 14px; color: #666; text-align: center;">
                        Sesi otorisasi ini berlaku selama perangkat tetap terhubung dengan pusat.
                    </p>
                </div>
                <div style="background-color: #f9f9f9; padding: 15px; text-align: center; border-top: 1px solid #eeeeee;">
                    <p style="font-size: 11px; color: #999; margin: 0;">&copy; 2026 PT Trusur Unggul Teknusa. Secure Gateway System.</p>
                </div>
            </div>
        </body>
    </html>
    """
    msg = MIMEMultipart()
    msg['From'] = SENDER_EMAIL
    msg['To'] = receiver_email
    msg['Subject'] = subject
    msg.attach(MIMEText(html_content, 'html'))
    try:
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls()
            server.login(SMTP_USERNAME, SMTP_PASSWORD)
            server.sendmail(SENDER_EMAIL, receiver_email, msg.as_string())
        return True
    except: return False

# --- 6. ENDPOINTS ---

class NewLogger(BaseModel):
    logger_id: str
    logger_name: str
    user_email: str

@app.get("/api/loggers", dependencies=[Depends(verify_api_key)])
def get_all_loggers():
    # Langsung kembalikan data tanpa cek expired time
    return loggers_db

@app.post("/api/loggers", dependencies=[Depends(verify_api_key)])
def create_logger(data: NewLogger):
    for l in loggers_db:
        if l["logger_id"] == data.logger_id:
            raise HTTPException(status_code=400, detail="ID Logger sudah ada!")
    new_entry = {
        "logger_id": data.logger_id,
        "logger_name": data.logger_name,
        "user_email": data.user_email,
        "activation_code": "None",
        "token": "None",
        "status": "Requesting",
        "created_at": datetime.now().isoformat()
    }
    loggers_db.append(new_entry)
    return new_entry

@app.post("/api/generate/{logger_id}", dependencies=[Depends(verify_api_key)])
def generate_code(logger_id: str):
    for l in loggers_db:
        if l["logger_id"] == logger_id:
            six_digit_code = ''.join(secrets.choice(string.digits) for _ in range(6))

            # Hapus logika penambahan waktu 60 menit
            l["token"] = secrets.token_urlsafe(16)
            l["activation_code"] = six_digit_code
            l["status"] = "Active"
            l["created_at"] = datetime.now().isoformat()

            send_verification_email(l["user_email"], six_digit_code, logger_id)
            return l
    raise HTTPException(status_code=404)

@app.delete("/api/loggers/{logger_id}", dependencies=[Depends(verify_api_key)])
def delete_logger(logger_id: str):
    global loggers_db, admin_notifications
    admin_notifications.append({
        "message": f"User {logger_id} telah logout. Data otomatis hangus!",
        "time": datetime.now().strftime("%H:%M:%S")
    })
    loggers_db = [l for l in loggers_db if l["logger_id"] != logger_id]
    return {"status": "success"}

@app.get("/api/notifications", dependencies=[Depends(verify_api_key)])
def get_notifications():
    global admin_notifications
    current_notifs = admin_notifications[:]
    admin_notifications.clear()
    return current_notifs

class VerifySchema(BaseModel):
    logger_id: str
    input_code: str

@app.post("/api/verify-code", dependencies=[Depends(verify_api_key)])
def verify_code(data: VerifySchema):
    for l in loggers_db:
        if l["logger_id"] == data.logger_id:
            # Hanya cek apakah kode cocok, tidak perlu cek waktu
            if l["activation_code"] != "None" and l["activation_code"] == data.input_code:
                return {"valid": True}
    return {"valid": False}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=8000)