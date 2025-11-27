--- DEPLOY.md ---
# Deployment Options
This file explains how to push the repo to GitHub and deploy the Orvigo app.

## 1) Push code to GitHub (your repo)
Replace `<your-repo-url>` with `https://github.com/PixelStackAgency/orvigo-website.git`.

```cmd
cd "C:\All In 1\Wajid Bhaw Project\Orvigo"
git init
git add .
git commit -m "Initial Orvigo scaffold"
git branch -M main
git remote add origin https://github.com/PixelStackAgency/orvigo-website.git
git push -u origin main
```

If you already have a repo, just set the remote and push.

## 2) Deploy full PHP app using Render (recommended for PHP)
Render supports Docker and PHP web services. This keeps frontend and backend together.

Steps:
1. Create a Render account and connect GitHub.
2. Create a new Web Service, choose your repo `orvigo-website`.
3. For Environment choose "Docker" (Render will build the Dockerfile in repo).
4. Set Build/Start commands: Dockerfile will be used.
5. Set Environment variables in Render dashboard (Settings -> Environment):
   - `ORVIGO_ADMIN_PASSWORD_HASH` (use `password_hash('yourpassword', PASSWORD_DEFAULT)` to generate)
   - `RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET`, `RAZORPAY_WEBHOOK_SECRET` (for payments)
6. Deploy. After successful build, your site will be live on Render's domain.

### Notes
- Ensure `storage/` and `logs/` are writable by the www-data user (Dockerfile sets ownership).
- Render will provide HTTPS automatically.

## 3) Split deployment: Frontend on Vercel, Backend on Render
If you prefer Vercel for the frontend (static assets) and Render for PHP backend:

1. Deploy backend to Render (as above).
2. In GitHub, configure Vercel to deploy the `public/` directory as a static site.
   - In Vercel project settings, set the build output directory to `public`.
   - Set rewrite rules or environment variables so frontend calls backend API endpoints on Render domain. Example in Vercel `vercel.json` (if needed):
     {
       "rewrites": [
         { "source": "/api/(.*)", "destination": "https://your-render-backend.example.com/api/$1" }
       ]
     }
3. Ensure CORS and requests are allowed from the Vercel domain on your backend.

## 4) Deploy to traditional shared hosting (cPanel)
1. Zip your repo (exclude `.git`, `storage`, `logs` if desired).
2. Upload via cPanel File Manager to `public_html` or set DocumentRoot to the `public` folder.
3. Ensure `storage/` and `logs/` are writable and create `.env` or set config values in a secure place.

## 5) Local testing with Docker
If you want to run locally using Docker:
```cmd
docker build -t orvigo-app .
docker run -p 8080:80 --name orvigo -d orvigo-app
```
Then open http://localhost:8080
