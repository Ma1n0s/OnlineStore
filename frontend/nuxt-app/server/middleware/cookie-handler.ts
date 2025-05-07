import { setCookie } from 'h3'

export default defineEventHandler(event => {
  // Обработка ответа от прокси и копирование куки в ответ клиенту
  event.node.res.on('finish', () => {
    const setCookieHeaders = event.node.res.getHeader('set-cookie')
    if (setCookieHeaders) {
      const cookies = Array.isArray(setCookieHeaders) ? setCookieHeaders : [setCookieHeaders]

      cookies.forEach(cookieValue => {
        // Конвертируем значение в строку и разбираем куки
        const cookie = String(cookieValue)
        const cookieParts = cookie.split(';')
        const nameValuePair = cookieParts[0]

        if (nameValuePair) {
          const [cookieName, cookieVal] = nameValuePair.split('=')
          if (cookieName && cookieVal) {
            // Если cookie имеет SameSite=None, то нужно также установить secure
            const hasSameSiteNone = cookie.includes('SameSite=None')

            setCookie(event, cookieName, cookieVal, {
              httpOnly: cookie.includes('HttpOnly'),
              // Всегда устанавливаем secure для SameSite=None
              secure: hasSameSiteNone ? true : cookie.includes('Secure'),
              // SameSite должен быть none для кросс-доменных запросов с credentails
              sameSite: hasSameSiteNone ? 'none' : 'lax',
              path: '/',
              maxAge: 60 * 60 * 24 * 7, // 7 дней
            })

            // Логируем для отладки
            console.log(
              `Cookie set: ${cookieName}, SameSite=${hasSameSiteNone ? 'none' : 'lax'}, Secure=${hasSameSiteNone ? true : cookie.includes('Secure')}`
            )
          }
        }
      })
    }
  })
})
