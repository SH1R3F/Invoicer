import i18n from '@/plugins/i18n'
import { isEmpty, isEmptyArray, isNullOrUndefined } from './index'

// 👉 Required Validator
export const requiredValidator = value => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return i18n.global.t('This field is required')

  return !!String(value).trim().length || i18n.global.t('This field is required')
}

// 👉 Email Validator
export const emailValidator = value => {
  if (isEmpty(value))
    return true
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  if (Array.isArray(value))
    return value.every(val => re.test(String(val))) || i18n.global.t('The Email field must be a valid email')

  return re.test(String(value)) || i18n.global.t('The Email field must be a valid email')
}

// 👉 Password Validator
export const passwordValidator = password => {
  const regExp = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*()]).{8,}/
  const validPassword = regExp.test(password)

  return (
    // eslint-disable-next-line operator-linebreak
    validPassword ||
    i18n.global.t('Field must contain at least one uppercase, lowercase, special character and digit with min 8 chars'))
}

// 👉 Confirm Password Validator
export const confirmedValidator = (value, target) => value === target || i18n.global.t('The Confirm Password field confirmation does not match')

// 👉 Between Validator
export const betweenValidator = (value, min, max) => {
  const valueAsNumber = Number(value)

  return (Number(min) <= valueAsNumber && Number(max) >= valueAsNumber) || i18n.global.t(`Enter number between {min} and {max}`, { min, max })
}

// 👉 Integer Validator
export const integerValidator = value => {
  if (isEmpty(value))
    return true
  if (Array.isArray(value))
    return value.every(val => /^-?[0-9]+$/.test(String(val))) || i18n.global.t('This field must be an integer')

  return /^-?[0-9]+$/.test(String(value)) || i18n.global.t('This field must be an integer')
}

// 👉 Regex Validator
export const regexValidator = (value, regex) => {
  if (isEmpty(value))
    return true
  let regeX = regex
  if (typeof regeX === 'string')
    regeX = new RegExp(regeX)
  if (Array.isArray(value))
    return value.every(val => regexValidator(val, regeX))

  return regeX.test(String(value)) || i18n.global.t('The Regex field format is invalid')
}

// 👉 Alpha Validator
export const alphaValidator = value => {
  if (isEmpty(value))
    return true

  return /^[A-Z]*$/i.test(String(value)) || i18n.global.t('The Alpha field may only contain alphabetic characters')
}

// 👉 URL Validator
export const urlValidator = value => {
  if (isEmpty(value))
    return true
  const re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/

  return re.test(String(value)) || i18n.global.t('URL is invalid')
}

// 👉 Length Validator
export const lengthValidator = (value, length) => {
  if (isEmpty(value))
    return true

  return String(value).length >= length || i18n.global.t(`The Min Character field must be at least {length} characters`, { length })
}

// 👉 Alpha-dash Validator
export const alphaDashValidator = value => {
  if (isEmpty(value))
    return true
  const valueAsString = String(value)

  return /^[0-9A-Z_-]*$/i.test(valueAsString) || i18n.global.t('All Character are not valid')
}

// 👉 Equals Validator
export const EqualsValidator = (value, expected) => {
  if (isEmpty(value))
    return true

  return value === expected || i18n.global.t('The confirmation field does not match the new password field')
}
