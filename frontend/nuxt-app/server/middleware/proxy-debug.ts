export default defineEventHandler(event => {
  const { req, res } = event.node
  const url = req.url || ''

  // Логируем API-запросы для отладки
  if (url.startsWith('/api/') || url.startsWith('/sanctum/')) {
    console.log(`Proxy request: ${req.method} ${url}`)

    // Добавляем заголовки для CORS и Sanctum
    if (!res.headersSent) {
      // Разрешаем все источники, или конкретные источники
      const origin = req.headers.origin || 'http://127.0.0.1:3000'
      res.setHeader('Access-Control-Allow-Origin', origin)
      res.setHeader('Access-Control-Allow-Credentials', 'true')
      res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
      res.setHeader(
        'Access-Control-Allow-Headers',
        'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-XSRF-TOKEN'
      )

      // Разрешаем все заголовки, отправленные в запросе
      const requestHeaders = req.headers['access-control-request-headers']
      if (requestHeaders) {
        res.setHeader('Access-Control-Allow-Headers', requestHeaders)
      }

      // Для OPTIONS запросов сразу возвращаем успешный ответ
      if (req.method === 'OPTIONS') {
        res.statusCode = 204
        res.end()
        return
      }
    }
  }
})
