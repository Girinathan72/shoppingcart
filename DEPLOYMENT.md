# Deployment Guide - Fresh Mart E-commerce

## Issues Resolved
- ✅ Removed duplicate file structure
- ✅ Added Netlify configuration
- ✅ Static site ready for deployment

## Deployment Options

### Option 1: Deploy to Netlify (Recommended)
1. Go to [netlify.com](https://netlify.com) and sign up
2. Click "New site from Git"
3. Connect your GitHub account
4. Select the `shoppingcart` repository
5. Netlify will auto-detect and deploy
6. Your site will be live at: `https://[your-site].netlify.app`

### Option 2: Deploy to Vercel
1. Go to [vercel.com](https://vercel.com)
2. Click "Add New Project"
3. Import your GitHub repository
4. Vercel will auto-detect and deploy
5. Your site will be live instantly

### Option 3: Deploy to GitHub Pages
1. Go to your repository settings
2. Navigate to "Pages"
3. Set source to "main" branch
4. Your site will be live at: `https://girinathan72.github.io/shoppingcart`

## Verify Deployment Works
After deploying, check:
- ✅ Home page loads: `https://your-site/index.html`
- ✅ Products page loads: `https://your-site/products.html`
- ✅ Cart page loads: `https://your-site/cart.html`
- ✅ Contact page loads: `https://your-site/contact.html`
- ✅ Images load correctly
- ✅ Add to cart functionality works
- ✅ Search filter works

## Fix GitHub Push Authentication
To push to GitHub, use one of these methods:

**Method 1: Personal Access Token (Easy)**
```powershell
git remote set-url origin https://YOUR_TOKEN@github.com/Girinathan72/shoppingcart.git
git push -u origin main
```
Get token from: GitHub → Settings → Developer Settings → Personal Access Tokens

**Method 2: SSH (Secure)**
```powershell
ssh-keygen -t ed25519 -C "your_email@example.com"
git remote set-url origin git@github.com:Girinathan72/shoppingcart.git
git push -u origin main
```

**Method 3: GitHub CLI**
```powershell
gh auth login
git push -u origin main
```

## Project Structure (Fixed)
```
├── index.html          (Home page)
├── products.html       (Products page)  
├── cart.html          (Shopping cart)
├── contact.html       (Contact form)
├── style.css          (Styling)
├── script.js          (Shopping cart logic)
├── images/            (Product images)
│   ├── honey.jpeg
│   ├── oil.jpeg
│   ├── rice.jpeg
│   ├── salt.jpeg
│   ├── sugar.jpeg
│   └── wheat.jpeg
├── netlify.toml       (Deployment config)
├── .gitignore         (Git ignore rules)
├── api.php            (Backend only - not deployed)
├── config.php         (Backend only - not deployed)
├── database.sql       (Database schema - not deployed)
└── mock-data.js       (For future API integration)
```

## Notes
- Static HTML/CSS/JS works on all platforms
- No PHP or database required for basic functionality
- Shopping cart uses localStorage (browser storage)
- If you need a real backend later, deploy api.php to a separate Node.js/PHP server

## Questions?
Check the deployment platform's documentation or contact support.
