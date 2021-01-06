export const formatSize = (size) => {
  if (typeof size !== 'number') size = NaN
  let count = 0
  while (size >= 1024) {
    size /= 1024
    count++
  }
  size = size.toFixed(2)
  size += [' B', ' KB', ' MB', ' GB', ' TB'][count]
  return size
}

export const isEmpty = (obj) => [Object, Array].includes((obj || {}).constructor) && !Object.entries(obj || {}).length

export const defaultValue = (value, defaultValue) => {
  switch (value) {
    case 'null':
    case 'undefined':
    case null:
    case undefined:
    case '':
      return defaultValue
    default:
      return value
  }
}


export const in_array = (needle, haystack, argStrict) => {
  let key = ''
  const strict = !!argStrict
  if (strict) {
    for (key in haystack) {
      if (haystack[key] === needle) {
        return true
      }
    }
  } else {
    for (key in haystack) {
      // eslint-disable-next-line
      if (haystack[key] == needle) {
        return true
      }
    }
  }

  return false
}

export const fileExtension = {
  image: ['ico', 'bmp', 'gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'tif', 'tiff', 'png', 'heic', 'webp'],
  audio: ['mp3', 'wma', 'flac', 'ape', 'wav', 'ogg', 'm4a'],
  office: ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'],
  txt: ['txt', 'bat', 'sh', 'php', 'asp', 'js', 'css', 'json', 'html', 'c', 'cpp', 'md', 'py', 'omf'],
  video: ['mp4', 'webm', 'mkv', 'mov', 'flv', 'blv', 'avi', 'wmv', 'm3u8', 'rm', 'rmvb'],
  zip: ['zip', 'rar', '7z', 'gz', 'tar'],
}