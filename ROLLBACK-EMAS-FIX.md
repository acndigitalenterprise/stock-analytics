# ROLLBACK PLAN: EMAS Search Filter Fix

## Change Summary
**File:** `app/Http/Controllers/StockAnalyticsController.php`
**Lines:** 147-185
**Purpose:** Allow EMAS and similar IDX stocks to appear in search results

## What Was Changed
**BEFORE (Strict Filter):**
```php
if ($exchange === 'IDX' || str_ends_with($symbol, '.JK')) {
    // Only stocks with exchange=IDX or suffix .JK
}
```

**AFTER (Relaxed Filter):**
```php
$isIDXStock = $exchange === 'IDX'
    || str_ends_with($symbol, '.JK')
    || $exchange === 'JKT'
    || ($queryUpper === $symbolUpper) // Exact match
    || (strlen($queryUpper) >= 3 && str_starts_with($symbolUpper, $queryUpper)); // Partial match
```

## Testing Checklist BEFORE Deploy
- [ ] Test search "EMAS" - should return results
- [ ] Test search "BBCA" - should still work (regression test)
- [ ] Test search "ANTM" - should return results
- [ ] Test search "TLKM" - should still work
- [ ] Check logs for match_type debug info

## Rollback Steps (If Issues Occur)

### Option 1: Git Revert (FASTEST)
```bash
# On production server
cd /home/forge/tickerai.app
git log --oneline -5  # Find commit hash
git revert <commit-hash> --no-edit
php artisan cache:clear
php artisan config:clear
```

### Option 2: Manual Patch Rollback
```bash
# Apply reverse patch
cd /home/forge/tickerai.app
git apply --reverse /tmp/emas-filter-fix.patch
php artisan cache:clear
```

### Option 3: Direct Code Fix
Replace lines 161-165 in `StockAnalyticsController.php` with:
```php
if ($exchange === 'IDX' || str_ends_with($symbol, '.JK')) {
```

## Potential Issues & Solutions

### Issue 1: Too Many Non-IDX Stocks Appear
**Symptom:** Search returns US stocks or other markets
**Fix:** Tighten filter by removing partial match (line 165)
```php
// Remove this line:
|| (strlen($queryUpper) >= 3 && str_starts_with($symbolUpper, $queryUpper));
```

### Issue 2: EMAS Still Not Appearing
**Symptom:** EMAS not in results even after fix
**Diagnosis:**
1. Check logs: `tail -100 storage/logs/laravel.log | grep "Yahoo Finance matched"`
2. Test Yahoo API directly: `curl "https://query1.finance.yahoo.com/v1/finance/search?q=EMAS"`
3. Check cache: May need `php artisan cache:clear`

**Fix:** Add to local fallback data

### Issue 3: Performance Degradation
**Symptom:** Search becomes slow
**Cause:** More complex matching logic
**Fix:** Add caching aggressive or revert to strict filter

## Success Criteria
✅ EMAS appears in search results
✅ Existing IDX stocks (.JK) still work
✅ No non-IDX stocks appear
✅ Search performance unchanged (<500ms)
✅ No errors in logs

## Emergency Contact
If production breaks:
1. **IMMEDIATE:** Run rollback Option 1 (git revert)
2. **VERIFY:** Test search on https://tickerai.app
3. **NOTIFY:** User of rollback completion

## Deployment Timestamp
- Local commit: [PENDING]
- Production deploy: [PENDING]
- Rollback status: NOT NEEDED ✅ / EXECUTED ❌

---
**Created:** 2025-10-03
**Author:** Claude Code
**Status:** READY FOR DEPLOYMENT
