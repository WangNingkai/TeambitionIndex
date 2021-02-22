import request from '../libs/request'

export const login = (params) => {
  return request.post('/api/login', params)
}
