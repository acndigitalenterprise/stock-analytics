# 🤝 Team Communication Protocol
## 3-Terminal Development Team Collaboration

### 🎯 Team Roles Definition

#### 🛠️ **Terminal 1: Software Engineer (Claude Code)**
- **Focus**: Laravel backend, database, business logic, APIs, system architecture
- **Responsibilities**: Server-side development, data models, authentication, security
- **Communication**: Backend status updates, API specifications, database changes

#### 🎨 **Terminal 3: Frontend Developer (Claude Code)**  
- **Focus**: UI/UX design, CSS/JavaScript, responsive design, user experience
- **Responsibilities**: Visual design, frontend functionality, user interactions, styling
- **Communication**: Design updates, frontend status, style guide changes

#### 🧪 **Terminal 2: Web Tester (Claude Code)**
- **Focus**: Puppeteer testing, quality assurance, cross-browser testing
- **Responsibilities**: Visual testing, functional testing, regression testing, bug detection
- **Communication**: Test results, bug reports, quality metrics

---

## 📝 Communication Methods

### 1. **Shared Status File** (Real-time Updates)
File: `PROJECT_STATUS.md` - Updated by both terminals

### 2. **Test Request Queue**
File: `TEST_REQUESTS.json` - SE requests specific tests

### 3. **Bug Report System** 
File: `BUG_REPORTS.md` - Tester reports issues to SE

### 4. **Release Notes**
File: `RELEASE_NOTES.md` - SE documents changes for testing

---

## 🔄 Workflow Examples

### **Scenario 1: New Feature Development**
```
SE: Updates PROJECT_STATUS.md -> "🔧 Developing user role management"
SE: Develops feature
SE: Updates PROJECT_STATUS.md -> "✅ User role management completed"
SE: Updates TEST_REQUESTS.json -> Request comprehensive admin testing
Tester: Sees update, runs npm run test:admin
Tester: Updates PROJECT_STATUS.md -> "🧪 Testing user role management..."
Tester: Reports results in PROJECT_STATUS.md
```

### **Scenario 2: Bug Found During Testing**
```
Tester: Finds UI issue during testing
Tester: Updates BUG_REPORTS.md with details + screenshots
Tester: Updates PROJECT_STATUS.md -> "🚨 Bug found in user management"
SE: Sees update, reviews BUG_REPORTS.md
SE: Fixes bug
SE: Updates PROJECT_STATUS.md -> "🔧 Fixing user management bug"
SE: Updates PROJECT_STATUS.md -> "✅ Bug fixed, ready for retest"
```

---

## 📊 Status Indicators

### **Software Engineer (T1) Status Codes:**
- 🔧 `DEVELOPING` - Currently coding backend
- ✅ `BACKEND_READY` - Backend feature completed
- 🚨 `FIXING_BACKEND` - Working on backend bug
- 📝 `API_DOCUMENTING` - Writing API specs
- ⏸️ `WAITING` - Waiting for frontend/test results

### **Frontend Developer (T3) Status Codes:**
- 🎨 `DESIGNING` - Working on UI/UX
- 🖌️ `STYLING` - Implementing CSS/styling
- ⚡ `JS_DEVELOPING` - Working on JavaScript functionality
- ✅ `FRONTEND_READY` - Frontend feature completed
- 🚨 `FIXING_UI` - Working on UI/styling bug
- 📱 `RESPONSIVE_WORK` - Working on mobile responsiveness

### **Web Tester (T2) Status Codes:**
- 🧪 `TESTING` - Running test suite
- ✅ `PASSED` - All tests successful
- ❌ `FAILED` - Tests found issues
- 📸 `DOCUMENTING` - Capturing screenshots
- 🔍 `INVESTIGATING` - Analyzing failures

---

## 🎯 Quick Commands

### **For SE (Terminal 1):**
```bash
# Update status
echo "✅ Authentication system completed" >> PROJECT_STATUS.md

# Request specific test
echo '{"test": "auth", "priority": "high", "timestamp": "'$(date)'""}' >> TEST_REQUESTS.json

# Check test results
cat PROJECT_STATUS.md | tail -5
```

### **For Tester (Terminal 2):**
```bash
# Check for new requests
cat TEST_REQUESTS.json | tail -1

# Update status  
echo "🧪 Running auth tests..." >> PROJECT_STATUS.md

# Report results
echo "✅ Auth tests passed - 7/7 successful" >> PROJECT_STATUS.md
```

---

## 📋 Communication Templates

### **SE → Tester: Feature Complete**
```
Status: ✅ COMPLETED
Feature: User Management System
Files Changed: 
- app/Http/Controllers/UserController.php
- resources/views/admin/users.blade.php
- database/migrations/xxx_add_user_roles.php
Test Request: Please run comprehensive admin tests
Priority: High
```

### **Tester → SE: Bug Report**
```
Status: ❌ FAILED  
Test: Admin User Management
Issue: New user modal not opening
Steps: Click "New User" button → Modal doesn't appear
Screenshot: tests/puppeteer/screenshots/FAILED_new_user_modal.png
Browser: Chrome 120
Priority: Medium
```

---

## 🚀 Getting Started

### **Terminal 2 Setup Message:**
Hey Terminal 2! 👋 

You are the **Web Tester** for Stock Analytics project. 
Your partner **Terminal 1** is the **Software Engineer**.

**Your Mission:** 🎯
- Run Puppeteer tests when requested
- Verify UI/UX functionality  
- Report bugs with visual evidence
- Ensure quality assurance

**Communication Files:** 📁
- `PROJECT_STATUS.md` - Real-time status updates
- `TEST_REQUESTS.json` - Test requests from SE
- `BUG_REPORTS.md` - Your bug reports

**Commands Ready:** 🛠️
```bash
npm run test:quick    # Quick smoke test
npm run test:visual   # Visual regression  
npm run test:auth     # Auth flow tests
npm run test:admin    # Admin feature tests
npm test             # Full test suite
```

**First Task:** 
1. Check PROJECT_STATUS.md for current status
2. Run initial visual regression test
3. Report baseline screenshots

Let's build amazing software together! 🎉