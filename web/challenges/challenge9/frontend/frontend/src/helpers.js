export const checkAuth = () => {
  const cookies = document.cookie.split(';');
  const connectSid = cookies.find(cookie => cookie.trim().startsWith('connect.sid='));
  return !!connectSid;
};

export const logout = () => {
  document.cookie = "connect.sid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
};
