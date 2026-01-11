from fastapi import FastAPI, HTTPException, Header, Depends
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from datetime import datetime, timedelta
import secrets
import string

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

def verify_api_key(x_api_key: str = Header(None)):
    if x_api_key != API_KEY_SECRET:
        raise HTTPException(status_code=403, detail="Forbidden: API Key Invalid")
    return x_api_key

# --- 3. DATABASE SIMULASI ---
loggers_db = [
    {
        "logger_id": "LOG-001",
        "logger_name": "Unit DAS Laptop Kamu",
        "user_email": "redninja1sr@gmail.com",
        "activation_code": "None",
        "token": "None",
        "status": "Pending",
        "created_at": None,
        "expired_at": None
    }
]

# --- 4. LOGIKA HELPER ---
def check_expiration(logger):
    if logger["expired_at"] and logger["status"] == "Active":
        expiry_time = datetime.strptime(logger["expired_at"], "%Y-%m-%d %H:%M:%S")
        if datetime.now() > expiry_time:
            logger["status"] = "Cancel"
            logger["token"] = "Expired"
            logger["activation_code"] = "Expired"
    return logger

# --- 5. ENDPOINT UNTUK DASHBOARD & DAS ---

class NewLogger(BaseModel):
    logger_id: str
    logger_name: str
    user_email: str

@app.get("/api/loggers", dependencies=[Depends(verify_api_key)])
def get_all_loggers():
    updated_data = [check_expiration(l) for l in loggers_db]
    return updated_data

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
        "status": "Requesting", # Otomatis Requesting agar muncul ungu di dashboard
        "created_at": datetime.now().isoformat(),
        "expired_at": None
    }
    loggers_db.append(new_entry)
    return new_entry

@app.post("/api/generate/{logger_id}", dependencies=[Depends(verify_api_key)])
def generate_code(logger_id: str):
    for l in loggers_db:
        if l["logger_id"] == logger_id:
            six_digit_code = ''.join(secrets.choice(string.digits) for _ in range(6))
            expiry_time = datetime.now() + timedelta(minutes=60)
            expiry_str = expiry_time.strftime("%Y-%m-%d %H:%M:%S")

            l["token"] = secrets.token_urlsafe(16)
            l["activation_code"] = six_digit_code
            l["expired_at"] = expiry_str
            l["status"] = "Active"
            l["created_at"] = datetime.now().isoformat()
            return l
    raise HTTPException(status_code=404, detail="Logger Not Found")

@app.delete("/api/loggers/{logger_id}", dependencies=[Depends(verify_api_key)])
def delete_logger(logger_id: str):
    global loggers_db
    loggers_db = [l for l in loggers_db if l["logger_id"] != logger_id]
    return {"status": "success"}

class VerifySchema(BaseModel):
    logger_id: str
    input_code: str

@app.post("/api/verify-code", dependencies=[Depends(verify_api_key)])
def verify_code(data: VerifySchema):
    for l in loggers_db:
        check_expiration(l)
        if l["logger_id"] == data.logger_id:
            if l["activation_code"] == data.input_code and l["status"] == "Active":
                return {"valid": True}
    return {"valid": False}