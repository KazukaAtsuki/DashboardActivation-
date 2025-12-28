from fastapi import FastAPI, HTTPException
from pydantic import BaseModel # Tambahkan ini
from datetime import datetime, timedelta
import secrets
import string

app = FastAPI()

# 1. Buat Model Data untuk input data baru
class LoggerSchema(BaseModel):
    logger_id: str
    logger_name: str

# Database simulasi
loggers_db = [
    {
        "logger_id": "LOG-001",
        "logger_name": "Sensor Jakarta",
        "token": "None",
        "activation_code": "None",
        "status": "Pending",
        "created_at": None
    }
]

@app.get("/api/loggers")
def get_loggers():
    return loggers_db

# 2. TAMBAHKAN FUNGSI INI: Pintu untuk menambah data baru
@app.post("/api/loggers")
def create_logger(data: LoggerSchema):
    # Cek apakah ID sudah ada
    for l in loggers_db:
        if l["logger_id"] == data.logger_id:
            raise HTTPException(status_code=400, detail="ID Logger sudah terdaftar!")

    # Buat data baru dengan format awal (Pending)
    new_logger = {
        "logger_id": data.logger_id,
        "logger_name": data.logger_name,
        "token": "None",
        "activation_code": "None",
        "status": "Pending",
        "created_at": None
    }
    loggers_db.append(new_logger)
    return {"message": "Data berhasil ditambahkan", "data": new_logger}

@app.post("/api/generate/{logger_id}")
def generate(logger_id: str):
    for logger in loggers_db:
        if logger["logger_id"] == logger_id:
            logger["token"] = ''.join(secrets.choice(string.ascii_letters + string.digits) for _ in range(16))
            logger["activation_code"] = "ACT-" + ''.join(secrets.choice(string.digits) for _ in range(4))
            logger["status"] = "Active"
            logger["created_at"] = datetime.now().isoformat()
            return logger
    raise HTTPException(status_code=404, detail="Logger not found")