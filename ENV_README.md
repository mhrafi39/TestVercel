# SerVora Frontend Environment Configuration

This document explains how to configure the frontend for different deployment environments.

## Environment Variables

The frontend uses the following environment variables:

- `VITE_API_BASE_URL`: The base URL for the backend API
- `VITE_STORAGE_BASE_URL`: The base URL for file storage
- `VITE_APP_ENV`: The current environment (development, local, production)

## Local Development Setup

1. Copy the example environment file:
   ```bash
   cp .env.example .env.local
   ```

2. Update `.env.local` with your local backend URL:
   ```
   VITE_API_BASE_URL=http://localhost:8000/api
   VITE_STORAGE_BASE_URL=http://localhost:8000/storage
   VITE_APP_ENV=local
   ```

3. Start the development server:
   ```bash
   npm run dev
   ```

## Production Deployment (Vercel)

For production deployment on Vercel:

1. Set environment variables in your Vercel dashboard or update `.env.production`:
   ```
   VITE_API_BASE_URL=https://your-backend-domain.com/api
   VITE_STORAGE_BASE_URL=https://your-backend-domain.com/storage
   VITE_APP_ENV=production
   ```

2. Deploy to Vercel:
   ```bash
   vercel --prod
   ```

## Environment File Priority

Vite loads environment files in the following order:
1. `.env.local` (loaded in all environments, should be ignored by git)
2. `.env.production` (loaded in production)
3. `.env` (loaded in all environments)

## Notes

- All environment variables must be prefixed with `VITE_` to be accessible in the frontend
- The `api.js` file contains fallback values for local development
- Make sure to update your backend URL in the production environment variables before deploying