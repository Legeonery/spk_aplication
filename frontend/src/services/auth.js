// services/auth.js
import axios from 'axios'

export async function login(email, password) {
  const res = await axios.post('http://127.0.0.1:8000/api/auth/login', {
    email,
    password,
  })
  const token = res.data.access_token
  localStorage.setItem('token', token)
  return token
}
export function logout() {
  // Удаляем токен из localStorage
  localStorage.removeItem('token')

  // Перенаправляем на страницу логина
  window.location.href = '/login' // или router.push('/login') если используешь vue-router
}
