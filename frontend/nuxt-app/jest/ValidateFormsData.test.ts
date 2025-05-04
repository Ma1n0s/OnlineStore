import { validate, validateEmail } from '~/components/Forms/helpers'
import { emailRegex } from '~/shared/regexp'

emailRegex.test(form.email)

validate
validateEmail
