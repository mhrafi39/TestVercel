import axios from "axios";

export const API_BASE = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api";
export const STORAGE_BASE = import.meta.env.VITE_STORAGE_BASE_URL || "http://localhost:8000/storage";

// Debug: Log the API base URL being used
console.log("🚀 API_BASE URL:", API_BASE);
console.log("🚀 Environment:", import.meta.env.MODE);
console.log("🚀 VITE_API_BASE_URL:", import.meta.env.VITE_API_BASE_URL);

export const getToken = () => {
  const userToken = localStorage.getItem("token");
  const adminToken = localStorage.getItem("admin_token");
  return userToken || adminToken;
};

export const isAdmin = () => !!localStorage.getItem("admin_token");

export const authHeaders = () => {
  const token = getToken();
  return {
    "Content-Type": "application/json",
    Authorization: token ? `Bearer ${token}` : "",
  };
};

export const fetchProfile = async () => {
  const token = getToken();
  if (!token) return null;

  const url = isAdmin()
    ? `${API_BASE}/admin/profile`
    : `${API_BASE}/profile`;

  try {
    const res = await axios.get(url, { headers: authHeaders() });
    return res.data.user || res.data.admin || null;
  } catch (err) {
    if (err.response && err.response.status === 401) {
      localStorage.removeItem(isAdmin() ? "admin_token" : "token");
    }
    return null;
  }
};
