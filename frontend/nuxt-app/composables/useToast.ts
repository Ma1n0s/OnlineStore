export const useToast = () => {
    const showToast = (message: string, type: 'success' | 'error' = 'success') => {
      const toast = document.createElement('div')
      toast.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
      } text-white`
      toast.textContent = message
      document.body.appendChild(toast)
  
      setTimeout(() => {
        toast.remove()
      }, 3000)
    }
  
    return { showToast }
  }