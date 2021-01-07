import request from '../libs/request'

export const shareList = (params) => {
  return request.get('/api/share', params)
}

export const createShare = (params) => {
  return request.post('/api/share', params)
}

export const delteShare = (id, params) => {
  return request.delete('/api/share/' + id, params)
}
