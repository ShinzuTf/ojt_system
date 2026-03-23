## 🔧 Template Update Required

Your templates have **split placeholders** (which are now fixed), but some still have **HARDCODED VALUES** that need to be replaced with placeholders.

### Templates to Update:

#### 1. **NBI ENDORSEMENT.docx**
   - **Line 13**: `"720 hours"` → Replace with `${REQUIRED_HOURS}`
   - This is the critical fix for showing actual student hours

#### 2. **NBI comletter single.docx**  
   - **Line 24**: `"Seven Hundred Twenty Hours (720hrs)"` → Should reference `${REQUIRED_HOURS}` 
   - Consider using: `"Seven Hundred Twenty Hours (${REQUIRED_HOURS}hrs)"`

#### 3. **PARENT consent.docx**
   - Currently only has `${COURSE}`
   - Consider adding: `${FULL_NAME}`, `${FIRST_NAME}`, `${OJT_START}`, `${OJT_END}`
   - This will make the consent form dynamic

---

### How to Fix:

1. **Open each template in Microsoft Word**
2. **Search for hardcoded values** (Ctrl+H for Find & Replace)
3. **Replace with placeholders** in the format: `${PLACEHOLDER_NAME}`
4. **Common values to fix:**
   - Numbers like "720", "720 hours" → `${REQUIRED_HOURS}`
   - Dates like "January 19, 2026" → `${OJT_START}` or `${OJT_END}`
   - Student names → `${FULL_NAME}` or `${SHORT_NAME}`
   - Company names → `${COMPANY_NAME}`
   - Courses → `${COURSE}`

5. **Save and test** by running: `php artisan templates:inspect "<template_name>"`

---

### Available Placeholders Reference:

**Student Info:**
- `${FULL_NAME}` - Last Name, First Name M. Suffix
- `${SHORT_NAME}` - First Name Last Name
- `${FIRST_NAME}`, `${MIDDLE_NAME}`, `${LAST_NAME}`, `${SUFFIX}`
- `${STUDENT_NUMBER}`
- `${COURSE}`
- `${YEAR_LEVEL}`

**OJT Info:**
- `${COMPANY_NAME}`
- `${COMPANY_ADDRESS}`
- `${COMPANY_EMAIL}`
- `${SUPERVISOR_NAME}`
- `${SUPERVISOR_CONTACT}`
- `${SUPERVISOR_TITLE}` (currently not in database - add to OjtInfo model if needed)
- `${OJT_START}` - Formatted: January 19, 2026
- `${OJT_END}` - Formatted: June 4, 2026
- `${REQUIRED_HOURS}`
- `${RENDERED_HOURS}`
- `${PROGRESS_PERCENT}`

**Dates:**
- `${CURRENT_DATE}` - February 20, 2026
- `${CURRENT_MONTH}` - February
- `${CURRENT_YEAR}` - 2026
- `${CURRENT_DAY}` - 20

---

### Next Steps:

1. ✅ Update the 3 templates with the missing placeholders
2. ✅ Run `php artisan templates:inspect` to verify all placeholders are continuous now
3. ✅ Test generation via `/test/document` page
4. ✅ Download and verify generated DOCX files
