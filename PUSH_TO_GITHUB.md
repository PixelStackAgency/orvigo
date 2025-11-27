--- PUSH_TO_GITHUB.md ---
# Push project to a new GitHub repo

1. Create a new repository on GitHub (do not initialize with README/LICENSE).
2. In your project root run (replace URL with your repo):

```cmd
cd "C:\All In 1\Wajid Bhaw Project\Orvigo"
git init
git add .
git commit -m "Initial Orvigo website scaffold"
git branch -M main
git remote add origin https://github.com/<your-username>/<your-repo>.git
git push -u origin main
```

3. After pushing, set repository secrets or add `.env` on server with values from `.env.example`.
