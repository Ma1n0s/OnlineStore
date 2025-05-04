import { emailRegex } from '~/shared/regexp'

export const validateEmail = (form: Record<any, any>) => {
  if (!form.email) {
    form.emailError = 'Пожалуйста, введите email'
    return false
  }

  const isValidEmail = emailRegex.test(form.email)

  if (!isValidEmail) {
    form.emailError = 'Введите корректный email'
    return false
  }

  form.emailError = ''
  return true
}

export const validate = (form: Record<any, any>) => {
  let valid = true

  const isValidEmail = emailRegex.test(form.email)

  if (!form.email) {
    form.emailError = 'Пожалуйста, введите email'
    valid = false
  } else if (!isValidEmail) {
    form.emailError = 'Введите корректный email'
    valid = false
  } else {
    form.emailError = ''
  }

  if (!form.password) {
    form.passwordError = 'Пожалуйста, введите пароль'
    valid = false
  } else if (form.password.length < 6) {
    form.passwordError = 'Пароль должен быть не менее 6 символов'
    valid = false
  } else {
    form.passwordError = ''
  }

  return valid
}
