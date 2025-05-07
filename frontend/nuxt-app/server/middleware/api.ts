export default defineEventHandler(event => {
  const { req } = event.node
  const url = req.url || ''

  // Проверяем, является ли запрос API или Sanctum запросом
  if (url.startsWith('/api/') || url.startsWith('/sanctum/')) {
    // Устанавливаем заголовок для обработки прокси
    event.node.req.headers['x-is-api-request'] = 'true'
  }
})
