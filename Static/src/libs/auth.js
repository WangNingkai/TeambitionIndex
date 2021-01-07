import Cookies from 'js-cookie'

const TOKEN_KEY = 'v_access_token'
/**
 * @param token
 */
export const setToken = (token) => {
  Cookies.set(TOKEN_KEY, token)
}
/**
 * @returns {boolean|*}
 */
export const getToken = () => {
  const token = Cookies.get(TOKEN_KEY)
  if (token) return token
  else return false
}
/**
 * @returns {*}
 */
export const removeToken = () => {
  return Cookies.remove(TOKEN_KEY)
}
