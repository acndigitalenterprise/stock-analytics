# Timeframe Feature Test Plan (1w & 1m)

## Test Environment
- **Production Server**: forge@206.189.95.134
- **Testing Period**: After deployment
- **Test Accounts**: Use existing user accounts
- **Trading Hours**: Monday-Friday 09:00-15:50 WIB

---

## 1. Regression Tests (Existing Features - 1h & 1d)

### Test 1.1: 1 Hour Timeframe (Scalping)
**Purpose**: Ensure existing 1h functionality still works

**Steps**:
1. Login to production site
2. Navigate to Requests page during trading hours
3. Click "New Request"
4. Enter stock code: `BBCA.JK`
5. Select timeframe: `1 Hour`
6. Submit request
7. Wait for AI analysis (max 2 minutes)

**Expected Results**:
- ✅ Request created successfully
- ✅ Yahoo Finance data fetched (interval=1h, range=1d)
- ✅ Technical analysis completes
- ✅ Entry price, Target 1 (~1.5%), Target 2 (~3%), Stop Loss (~2%) calculated
- ✅ Monitoring duration = 1 hour from creation time
- ✅ AI advice mentions "scalping strategy"
- ✅ Job timeout = 120 seconds (2 minutes)

**Pass Criteria**: All expected results met, no errors in logs

---

### Test 1.2: 1 Day Timeframe (Day Trading)
**Purpose**: Ensure existing 1d functionality still works

**Steps**:
1. Login to production site
2. Navigate to Requests page during trading hours
3. Click "New Request"
4. Enter stock code: `TLKM.JK`
5. Select timeframe: `1 Day`
6. Submit request
7. Wait for AI analysis (max 5 minutes)

**Expected Results**:
- ✅ Request created successfully
- ✅ Yahoo Finance data fetched (interval=1d, range=1d)
- ✅ Technical analysis completes
- ✅ Entry price, Target 1 (~1.5%), Target 2 (~3%), Stop Loss (~2%) calculated
- ✅ Monitoring duration = 1 day from creation time
- ✅ AI advice mentions "day trading strategy"
- ✅ Job timeout = 300 seconds (5 minutes)

**Pass Criteria**: All expected results met, no errors in logs

---

## 2. New Feature Tests (1w & 1m)

### Test 2.1: 1 Week Timeframe (Swing Trading)
**Purpose**: Verify new 1w timeframe works correctly

**Steps**:
1. Login to production site
2. Navigate to Requests page during trading hours
3. Click "New Request"
4. Verify dropdown shows "1 Week" option
5. Enter stock code: `ASII.JK`
6. Select timeframe: `1 Week`
7. Submit request
8. Wait for AI analysis (max 10 minutes)
9. Check database: `monitoring_until` should be 7 days from now

**Expected Results**:
- ✅ Dropdown shows "1 Week" option
- ✅ Request created with timeframe='1w'
- ✅ Yahoo Finance data fetched (interval=1wk, range=1mo)
- ✅ Technical analysis completes
- ✅ Entry price, Target 1 (~5%), Target 2 (~10%), Stop Loss (~3%) calculated
- ✅ Monitoring duration = 1 week (7 days) from creation time
- ✅ AI advice mentions "swing trading strategy with 1-week timeframe"
- ✅ Job timeout = 600 seconds (10 minutes)
- ✅ Timeframe displayed as "1w" in table

**Database Verification**:
```sql
SELECT id, stock_code, timeframe, entry_price, target_1, target_2, stop_loss,
       monitoring_until, created_at, result
FROM requests
WHERE stock_code = 'ASII.JK' AND timeframe = '1w'
ORDER BY created_at DESC LIMIT 1;
```

**Pass Criteria**:
- Target 1 is ~5% above entry price
- Target 2 is ~10% above entry price
- Stop Loss is ~3% below entry price
- `monitoring_until` = `created_at` + 7 days

---

### Test 2.2: 1 Month Timeframe (Position Trading)
**Purpose**: Verify new 1m timeframe works correctly

**Steps**:
1. Login to production site
2. Navigate to Requests page during trading hours
3. Click "New Request"
4. Verify dropdown shows "1 Month" option
5. Enter stock code: `BMRI.JK`
6. Select timeframe: `1 Month`
7. Submit request
8. Wait for AI analysis (max 15 minutes)
9. Check database: `monitoring_until` should be 30 days from now

**Expected Results**:
- ✅ Dropdown shows "1 Month" option
- ✅ Request created with timeframe='1m'
- ✅ Yahoo Finance data fetched (interval=1mo, range=3mo)
- ✅ Technical analysis completes
- ✅ Entry price, Target 1 (~15%), Target 2 (~25%), Stop Loss (~7%) calculated
- ✅ Monitoring duration = 1 month (30 days) from creation time
- ✅ AI advice mentions "position trading strategy with 1-month timeframe"
- ✅ Job timeout = 900 seconds (15 minutes)
- ✅ Timeframe displayed as "1m" in table

**Database Verification**:
```sql
SELECT id, stock_code, timeframe, entry_price, target_1, target_2, stop_loss,
       monitoring_until, created_at, result
FROM requests
WHERE stock_code = 'BMRI.JK' AND timeframe = '1m'
ORDER BY created_at DESC LIMIT 1;
```

**Pass Criteria**:
- Target 1 is ~15% above entry price
- Target 2 is ~25% above entry price
- Stop Loss is ~7% below entry price
- `monitoring_until` = `created_at` + 1 month

---

## 3. Edge Cases & Error Handling

### Test 3.1: Invalid Timeframe Submission
**Steps**:
1. Try to submit POST request with timeframe='2w' (not allowed)

**Expected Result**:
- ✅ Validation error: "Timeframe must be either 1h, 1d, 1w, or 1m"

---

### Test 3.2: Job Timeout Test (1m timeframe)
**Steps**:
1. Create request with 1m timeframe for stock with limited data
2. Monitor job execution time

**Expected Result**:
- ✅ Job completes within 900 seconds OR times out gracefully
- ✅ No hanging jobs in queue

---

### Test 3.3: Price Monitoring - Timeout Check
**Steps**:
1. Create a 1w request
2. After 7 days, check if status changes to TIMEOUT (if no target hit)

**Expected Result**:
- ✅ After monitoring_until passes, status = 'TIMEOUT'
- ✅ `result_achieved_at` is set

---

## 4. UI/UX Tests

### Test 4.1: Dropdown Display
**Steps**:
1. Open New Request modal
2. Check timeframe dropdown

**Expected Result**:
- ✅ 4 options visible: "1 Hour", "1 Day", "1 Week", "1 Month"
- ✅ Options in logical order (shortest to longest)

---

### Test 4.2: Table Display
**Steps**:
1. Create requests with all 4 timeframes
2. View Requests table

**Expected Result**:
- ✅ TF column shows: "1h", "1d", "1w", "1m" correctly
- ✅ No formatting issues

---

### Test 4.3: Mobile Responsive
**Steps**:
1. Open site on mobile device
2. Create new request with 1w timeframe

**Expected Result**:
- ✅ Dropdown works on mobile
- ✅ Timeframe displays correctly in mobile cards

---

## 5. Performance Tests

### Test 5.1: Queue Processing Time
**Steps**:
1. Create 5 requests simultaneously with different timeframes
2. Monitor queue processing

**Expected Result**:
- ✅ Jobs process in order
- ✅ No memory leaks
- ✅ Each job respects its timeout limit

---

### Test 5.2: Database Query Performance
**Steps**:
1. Check slow query log after creating 10+ requests with 1w/1m

**Expected Result**:
- ✅ No slow queries (< 1 second)
- ✅ ENUM index works correctly

---

## 6. Integration Tests

### Test 6.1: Email Notification (1w/1m)
**Steps**:
1. Create 1w request
2. Check if email sent with correct timeframe context

**Expected Result**:
- ✅ Email mentions "swing trading" for 1w
- ✅ Email mentions "position trading" for 1m
- ✅ Targets display correct percentages

---

### Test 6.2: Price Monitoring Service
**Steps**:
1. Create 1w request with entry_price
2. Manually trigger: `php artisan monitor:prices`
3. Check logs

**Expected Result**:
- ✅ Monitoring service picks up 1w requests
- ✅ Monitoring continues until monitoring_until date
- ✅ Status updates correctly (WIN/LOSS/TIMEOUT)

---

## 7. Post-Deployment Checklist

- [ ] Migration executed successfully on production
- [ ] All 4 regression tests passed (1h, 1d)
- [ ] All 2 new feature tests passed (1w, 1m)
- [ ] No errors in Laravel logs (`storage/logs/laravel.log`)
- [ ] No errors in queue worker logs
- [ ] Queue jobs processing normally
- [ ] Email notifications working
- [ ] Price monitoring cron job running
- [ ] UI displays correctly on desktop
- [ ] UI displays correctly on mobile
- [ ] No performance degradation

---

## Test Results Template

### Test Execution Date: _____________
### Tester: _____________

| Test ID | Test Name | Status | Notes |
|---------|-----------|--------|-------|
| 1.1 | 1h Regression | ⬜ Pass / ⬜ Fail |  |
| 1.2 | 1d Regression | ⬜ Pass / ⬜ Fail |  |
| 2.1 | 1w New Feature | ⬜ Pass / ⬜ Fail |  |
| 2.2 | 1m New Feature | ⬜ Pass / ⬜ Fail |  |
| 3.1 | Invalid Input | ⬜ Pass / ⬜ Fail |  |
| 3.2 | Job Timeout | ⬜ Pass / ⬜ Fail |  |
| 3.3 | Monitoring Timeout | ⬜ Pass / ⬜ Fail |  |
| 4.1 | Dropdown UI | ⬜ Pass / ⬜ Fail |  |
| 4.2 | Table Display | ⬜ Pass / ⬜ Fail |  |
| 4.3 | Mobile Responsive | ⬜ Pass / ⬜ Fail |  |
| 5.1 | Queue Performance | ⬜ Pass / ⬜ Fail |  |
| 5.2 | DB Performance | ⬜ Pass / ⬜ Fail |  |
| 6.1 | Email Integration | ⬜ Pass / ⬜ Fail |  |
| 6.2 | Price Monitoring | ⬜ Pass / ⬜ Fail |  |

**Overall Result**: ⬜ PASS / ⬜ FAIL

**Issues Found**:
-

**Recommendations**:
-

---

## Rollback Plan

If critical issues found:

```bash
# On production server
cd /home/forge/ai-stock-analytics

# Rollback migration
php artisan migrate:rollback --step=1

# Revert code changes
git log --oneline -5
git revert <commit-hash>

# Restart services
php artisan queue:restart
php artisan cache:clear
```

---

## Success Criteria

✅ **Feature is production-ready when**:
1. All regression tests pass (1h, 1d unchanged)
2. Both new timeframes work correctly (1w, 1m)
3. No errors in production logs for 24 hours
4. UI displays correctly on all devices
5. Performance metrics within acceptable range
6. Email notifications working
7. Price monitoring service functioning

---

**Document Version**: 1.0
**Created**: 2025-10-27
**Last Updated**: 2025-10-27
