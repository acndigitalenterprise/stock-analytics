# Qwen Integration Setup Guide

## Status: Ready for Deployment (Waiting for API Key)

All code for Qwen (Alibaba AI) integration has been prepared locally but **NOT yet committed or deployed** because you need to register for a Qwen API key first.

---

## 🎯 What's Ready

### 1. Files Created Locally (Not Committed Yet)

#### `app/Services/QwenService.php`
- Complete service class for Qwen API integration
- Similar structure to ChatGPTService
- Handles stock analysis with technical indicators
- Includes error handling and timeout configuration

#### `database/migrations/2025_10_31_080000_add_advice_qwen_column_to_requests_table.php`
- Adds `advice_qwen` TEXT column to `requests` table
- Positioned after `advice_chatgpt` column
- Includes rollback method

### 2. Files Modified Locally (Not Committed Yet)

#### `app/Jobs/GenerateStockAdvice.php`
- Updated to call all 3 AI services (Claude + ChatGPT + Qwen)
- Saves all 3 advices to database
- Includes comprehensive logging

#### `config/services.php`
- Added Qwen configuration array
- Includes API key, base URL, model, max tokens, timeout

### 3. Files Already Deployed ✅

#### `resources/views/Requests/requestdetail.blade.php`
- Tabbed interface with 3 tabs: Claude, ChatGPT, Qwen
- Already deployed and working
- Qwen tab shows "Qwen analysis not available" until API key configured

#### `public/Requests/requestdetail.css`
- Tab styling with orange accent color
- Mobile-responsive design
- Smooth animations

#### `public/Requests/requestdetail.js`
- Tab switching functionality
- Already working in production

---

## 📋 Steps to Complete Qwen Integration

### Step 1: Register for Qwen API Key

1. Visit: **https://dashscope.aliyun.com/**
2. Create an Alibaba Cloud account (or login if you have one)
3. Navigate to DashScope console
4. Generate an API key for Qwen services

**Note**: You may need to verify your identity and possibly add payment method.

---

### Step 2: Add API Key to Production `.env`

Once you receive your API key, add these lines to your production `.env` file:

```env
# Qwen (Alibaba Cloud DashScope) Configuration
QWEN_API_KEY=your_api_key_here
QWEN_BASE_URL=https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation
QWEN_MODEL=qwen-plus
QWEN_MAX_TOKENS=1000
QWEN_TIMEOUT=30
```

**Available Models**:
- `qwen-plus` - Recommended (balanced performance/cost)
- `qwen-turbo` - Faster but less capable
- `qwen-max` - Most capable but more expensive

---

### Step 3: Deploy Qwen Integration Files

After adding the API key to `.env`, notify Claude Code to deploy the integration:

**Command to tell Claude Code**:
```
Saya sudah dapat API key Qwen. Tolong deploy Qwen integration sekarang.
```

Claude Code will:
1. Commit the 4 pending Qwen files
2. Push to GitHub
3. Run the migration to add `advice_qwen` column
4. Restart queue workers
5. Test the integration

---

## 🔍 What Will Happen After Deployment

### Database Changes
- `requests` table will have new `advice_qwen` column
- Existing records will have `NULL` for this column
- New requests will get all 3 AI opinions

### User Experience
- Request detail page shows 3 tabs (already live)
- Users can compare Claude vs ChatGPT vs Qwen opinions
- Each AI provides different perspective on the same stock

### System Behavior
- `GenerateStockAdvice` job calls all 3 APIs
- If one API fails, others continue
- Qwen failures won't break the system
- All 3 opinions stored separately in database

---

## 📊 Current System Status

### Already Deployed ✅
- Triple AI tab UI (Claude, ChatGPT, Qwen)
- Tab switching functionality
- Mobile-responsive design
- Qwen tab shows "not available" message

### Waiting for API Key ⏳
- QwenService integration
- Database migration for advice_qwen
- GenerateStockAdvice job update
- config/services.php update

### Next Steps
1. You register for Qwen API key
2. You add key to production .env
3. You notify Claude Code to deploy
4. Claude Code completes deployment
5. System generates 3 AI opinions for all new requests

---

## 🎨 UI Preview

The tabbed interface is already live with this structure:

```
Analytics
┌─────────┬──────────┬────────┐
│ Claude  │ ChatGPT  │ Qwen   │ ← Tab Navigation
├─────────┴──────────┴────────┤
│                              │
│  [AI Opinion Content Here]   │ ← Tab Content Panel
│                              │
└──────────────────────────────┘
```

**Features**:
- Orange highlight on active tab
- Smooth fade-in animation when switching
- Mobile: horizontal scrolling for tabs
- Icons for each AI service

---

## 💡 Benefits of Triple AI Opinion

1. **Diverse Perspectives**: Get 3 different AI viewpoints on the same stock
2. **Confidence Validation**: When all 3 AIs agree, higher confidence
3. **Risk Mitigation**: Spot when AIs disagree on analysis
4. **Model Comparison**: See which AI performs better over time
5. **Redundancy**: If one API fails, others still work

---

## 🔗 Related Files Already Deployed

These files were deployed in previous commits and support the Qwen integration:

- ✅ `app/Services/ChatGPTService.php` - Handles Claude + ChatGPT dual advice
- ✅ `app/Services/TechnicalAnalysisService.php` - Provides technical indicators
- ✅ `app/Services/PriceMonitoringService.php` - Monitors price targets
- ✅ Timeframe-aware thresholds (1h/1d/1w/1m)
- ✅ Queue-based job processing

---

## 📝 Notes

- **API Costs**: Each request will call 3 AI APIs, so costs will increase
- **Performance**: Jobs may take slightly longer (3 API calls vs 2)
- **Timeout**: Each AI has 30s timeout, total ~90s for all 3
- **Fallback**: System continues if Qwen fails (shows "not available")
- **Monitoring**: Uses Claude's deterministic advice for price monitoring (consistent format)

---

## ⚠️ Important Reminders

1. **Don't commit yet** - Wait until you have the API key
2. **Test in production** - After deployment, create a test request
3. **Monitor logs** - Check Laravel logs for Qwen API responses
4. **Check costs** - Monitor your Alibaba Cloud billing
5. **API limits** - Be aware of rate limits on Qwen API

---

## 📞 Support

If you encounter issues after deployment:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check queue worker status: `php artisan queue:work`
3. Verify .env configuration
4. Test Qwen API directly with curl
5. Contact Claude Code for troubleshooting

---

**Last Updated**: 2025-10-31
**Status**: Awaiting Qwen API Key Registration
