# DOCUMENTATION ORGANIZATION RULES

## **CRITICAL RULE: MD FILES PLACEMENT**

**ALL new `.md` files MUST be created in the `MD files/` folder.**

### **Directory Structure:**

```
c:\wamp64\www\
├── MD files/           ✅ ALL .md files go here
│   ├── *.md           ✅ Documentation files
│   └── *.md           ✅ Technical guides
├── app/               ❌ NO .md files here
├── docs/              ❌ NO .md files here (legacy - moved to MD files/)
├── public/            ❌ NO .md files here
└── *.md               ❌ NO .md files in root
```

### **MANDATORY PROCESS:**

1. **Before creating any documentation:**

   - Navigate to `MD files/` folder
   - Use descriptive, consistent naming
   - Use UPPERCASE for important documents

2. **Naming Conventions:**

   - `FEATURE_NAME_IMPLEMENTATION.md` - Implementation guides
   - `SYSTEM_NAME_COMPLETE.md` - Completion summaries
   - `COMPONENT_FIX_SUMMARY.md` - Fix documentation
   - `README_COMPONENT.md` - Component documentation

3. **Forbidden Locations:**
   - ❌ Root directory (`c:\wamp64\www\*.md`)
   - ❌ docs/ directory (`c:\wamp64\www\docs\*.md`)
   - ❌ Any subdirectory except `MD files/`

### **Benefits of This Organization:**

- ✅ **Centralized documentation** - All docs in one location
- ✅ **Cleaner repository structure** - No scattered .md files
- ✅ **Easier maintenance** - Single location to manage
- ✅ **Better navigation** - Clear separation of code and docs
- ✅ **Consistent organization** - Predictable file locations

### **Enforcement:**

This rule is enforced through:

- GitHub Copilot instructions
- Development workflow guidelines
- Code review requirements
- Automated organization scripts

**NO EXCEPTIONS:** Every new .md file MUST go in `MD files/` folder.
