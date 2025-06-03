export function useDebounceFn(fn, delay = 500) {
  let timeoutId = null

  return function (...args) {
    clearTimeout(timeoutId)
    return new Promise(resolve => {
      timeoutId = setTimeout(async () => {
        try {
          const result = await fn(...args)
          resolve(result)
        } catch (error) {
          console.error('Debounced function error:', error)
          resolve(null)
        }
      }, delay)
    })
  }
}
