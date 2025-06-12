export function useDebounceList(fn, delay = 500, finalFunction = () => {}) {
  const dataMap = new Map() // { id: { data, timer } }

  return function (id, newData) {
    // Если уже есть запись для этого id, очищаем старый таймер
    if (dataMap.has(id)) {
      const { timer } = dataMap.get(id)
      clearTimeout(timer)
    }

    // Сохраняем новые данные и устанавливаем таймер
    const timer = setTimeout(async () => {
      try {
        const currentData = dataMap.get(id)?.data
        if (currentData !== undefined) {
          await fn(currentData) // Передаем id и актуальные данные
        }
      } catch (error) {
        console.error('Debounced function error:', error)
      } finally {
        dataMap.delete(id) // Очищаем запись после выполнения
        if (dataMap.size === 0) {
          await finalFunction()
        }
      }
    }, delay)

    dataMap.set(id, { data: newData, timer })
  }
}
