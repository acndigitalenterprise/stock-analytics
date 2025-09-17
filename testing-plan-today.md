# Testing Plan - 17 September 2025

## Test Cases untuk Hari Ini:

### **Timeframe 1 Hour Tests:**
- ✅ Buat request baru dengan timeframe 1h
- ⏰ Expected: Status berubah ke TIMEOUT setelah 1 jam
- 📍 Monitor: Request akan timeout 1 jam setelah dibuat

### **Timeframe 1 Day Tests:**
- ✅ Buat request baru dengan timeframe 1d
- ⏰ Expected: Status berubah ke TIMEOUT setelah 24 jam (besok 18 Sep)
- 📍 Monitor: Request akan timeout besok di waktu yang sama

### **Monitoring Schedule:**

**09:00 WIB** - Cron job mulai berjalan
**Setiap 5 menit (09:05, 09:10, 09:15...)** - Auto monitoring
**Setiap jam (10:00, 11:00, 12:00...)** - Timeout processing

### **Manual Check Points:**

1. **Setelah 1 jam dari request dibuat:**
   - Check apakah status berubah dari MONITORING → TIMEOUT
   - Verify di https://tickerai.app/requests/[ID]

2. **Besok (18 Sep) di waktu yang sama:**
   - Check request timeframe 1d apakah timeout

### **Monitoring Commands:**
```sql
-- Check active monitoring requests
SELECT id, created_at, monitoring_until, result, timeframe
FROM requests
WHERE result = 'MONITORING'
ORDER BY created_at DESC;

-- Check recent timeouts
SELECT id, created_at, monitoring_until, result_achieved_at, result, timeframe
FROM requests
WHERE result = 'TIMEOUT'
AND DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

### **Success Criteria:**
- ✅ Request 1h: Status MONITORING → TIMEOUT setelah 1 jam
- ✅ Request 1d: Status tetap MONITORING sampai besok, lalu TIMEOUT setelah 24 jam
- ✅ No manual intervention needed
- ✅ Automatic processing via cron job