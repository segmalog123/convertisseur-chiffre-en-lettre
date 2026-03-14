---
name: git-workflow
description: Enforces the no-auto-commit policy. Use this skill whenever you are about to run git add, git commit, git push, git tag, or git reset. Never auto-commit or push changes to git without an explicit admin order.
---

# Git Workflow — No Auto-Commit Policy

## Rule: NEVER Commit or Push Automatically

After completing any code changes — no matter how small — **do NOT run `git add`, `git commit`, or `git push` automatically**.

You MUST wait for an explicit admin instruction such as:
- "commit and push"
- "push to git"
- "commit with message [...]"
- "tag and push vX.X.X"

## What You CAN Do Automatically

- Read git status: `git status`
- Read git log: `git log --oneline -N`
- Read diffs: `git diff HEAD`
- All file edits (PHP, JS, CSS, MD) are fine without a commit

## What Requires Admin Order

| Action | Requires Admin Order? |
|---|---|
| `git add` | ✅ Yes |
| `git commit` | ✅ Yes |
| `git push` | ✅ Yes |
| `git tag` | ✅ Yes |
| `git reset --hard` | ✅ Yes |
| File edits | ❌ No |
| `git status` / `git log` | ❌ No |

## When Admin Orders a Commit

Follow the user's exact commit message. If no message is given, propose one and confirm before committing.

Standard flow when ordered:
```bash
git add .
git commit -m "feat: [description]"
git push origin main
```

If the admin requests a version tag:
```bash
git tag -f vX.X.X
git push origin vX.X.X --force
```

## Why This Rule Exists

The production WordPress site pulls directly from the `main` branch on GitHub. Pushing unreviewed changes can immediately break the live site. All commits must be intentional and admin-approved.
