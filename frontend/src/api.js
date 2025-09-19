import axios from "axios";

export const API_BASE = "https://testvercel-production-7707.up.railway.app/api";
export const STORAGE_BASE = "https://testvercel-production-7707.up.railway.app/storage";

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
