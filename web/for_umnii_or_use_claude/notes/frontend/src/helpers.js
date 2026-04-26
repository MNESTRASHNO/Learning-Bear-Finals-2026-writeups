export const checkAuth = () => {
  const cookies = document.cookie.split(';')
  const tokenCookie = cookies.find(cookie => cookie.trim().startsWith('token='))
  
  if (!tokenCookie) {
    return false
  }

  return true
}