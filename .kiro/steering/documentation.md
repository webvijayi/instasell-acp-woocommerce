---
inclusion: always
---

# Documentation Standards

## Markdown File Management

### Repository Documentation Rules

**IMPORTANT**: Only `README.md` should be committed to the repository and included in distributions.

#### Files to Include
- `README.md` - Main project documentation (REQUIRED)

#### Files to Exclude
All other `.md` files should be excluded from:
- Git repository (via `.gitignore`)
- Distribution packages (via `.distignore` and `build-exclude.txt`)

This includes but is not limited to:
- `WORDPRESS-ORG-FIXES.md`
- `WORDPRESS-ORG-READY.md`
- `BUILD-GUIDE.md`
- `INSTALL.md`
- `LOCAL-SETUP.md`
- `MANUAL-BUILD-INSTRUCTIONS.md`
- `PRE-PUSH-CHECKLIST.md`
- `QUICKSTART.md`
- `RELEASE-CHECKLIST.md`
- Any other `.md` files except `README.md`

### Rationale

1. **Clean Repository**: Keeps the repository focused on essential documentation
2. **WordPress.org Compliance**: Reduces unnecessary files in plugin submissions
3. **User Experience**: End users only need README.md for basic information
4. **Maintainability**: Reduces clutter and confusion

### Implementation

The following files enforce this rule:

**`.gitignore`**:
```
# Exclude all .md files except README.md
*.md
!README.md
```

**`.distignore`**:
```
# Exclude all .md files except README.md
*.md
!README.md
```

**`build-exclude.txt`**:
```
# Exclude all .md files except README.md
*.md
!README.md
```

### When Creating New Documentation

If you need to create temporary documentation files (guides, checklists, etc.):
1. Create them with `.md` extension
2. They will automatically be excluded from git and distributions
3. Keep them locally for development reference only
4. Do NOT manually add them to git or distribution files

### Exception Process

If a specific `.md` file MUST be included:
1. Document the reason in this steering file
2. Add explicit exception to `.gitignore`: `!filename.md`
3. Add explicit exception to `.distignore`: `!filename.md`
4. Add explicit exception to `build-exclude.txt`: `!filename.md`
