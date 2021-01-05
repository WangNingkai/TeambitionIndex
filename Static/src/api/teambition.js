import request from '../libs/request'

export const fetchList = (params) => {
    return request.post('/api/nodes', params)
}

export const fetchItem = (params) => {
    return request.post('/api/node', params)
}
