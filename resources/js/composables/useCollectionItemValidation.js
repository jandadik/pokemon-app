import { ref, computed, watch } from 'vue'
import { getCurrentInstance } from 'vue'

export function useCollectionItemValidation(formData) {
  const instance = getCurrentInstance()
  const $t = instance.appContext.config.globalProperties.$t
  
  const errors = ref({})
  const isValidating = ref(false)
  
  // Validní hodnoty podle backend rules
  const validConditions = ['mint', 'near_mint', 'excellent', 'good', 'played', 'poor']
  const validLanguages = ['english', 'german', 'french', 'czech', 'japanese', 'spanish', 'italian', 'portuguese', 'chinese', 'korean']
  const validGradingCompanies = ['psa', 'bgs', 'cgc', 'sgc']
  
  // Jednotlivé validační funkce
  const validateCondition = (value) => {
    if (!value) {
      return $t('validation.required', { attribute: $t('validation.attributes.condition') })
    }
    if (!validConditions.includes(value)) {
      return $t('validation.custom.condition.invalid')
    }
    return null
  }
  
  const validateLanguage = (value) => {
    if (!value) {
      return $t('validation.required', { attribute: $t('validation.attributes.language') })
    }
    if (!validLanguages.includes(value)) {
      return $t('validation.custom.language.invalid')
    }
    return null
  }
  
  const validateQuantity = (value) => {
    if (!value) {
      return $t('validation.required', { attribute: $t('validation.attributes.quantity') })
    }
    const num = parseInt(value)
    if (isNaN(num) || num < 1 || num > 999) {
      return $t('validation.between.numeric', { 
        attribute: $t('validation.attributes.quantity'), 
        min: 1, 
        max: 999 
      })
    }
    return null
  }
  
  const validatePurchasePrice = (value) => {
    if (value && value !== '') {
      const num = parseFloat(value)
      if (isNaN(num) || num < 0) {
        return $t('validation.min.numeric', { 
          attribute: $t('validation.attributes.purchase_price'), 
          min: 0 
        })
      }
    }
    return null
  }
  
  const validateGradeCompany = (value) => {
    if (value && !validGradingCompanies.includes(value)) {
      return $t('validation.custom.grade_company.invalid')
    }
    return null
  }
  
  const validateGradingCert = (value, gradeCompany) => {
    // Nejdřív kontrola formátu/délky
    if (value) {
      // grading_cert je string, ne číslo - jen kontrola délky
      if (value.length > 50) {
        return $t('validation.max.string', { 
          attribute: $t('validation.attributes.grading_cert'), 
          max: 50 
        })
      }
    }
    
    // Pak cross-field validace
    if (gradeCompany && !value) {
      return $t('validation.custom.grade_value.required_with_company')
    }
    if (value && !gradeCompany) {
      return $t('validation.custom.grade_company.required_with_value')
    }
    
    return null
  }
  
  const validateLocation = (value) => {
    if (value && value.length > 100) {
      return $t('validation.max.string', { 
        attribute: $t('validation.attributes.location'), 
        max: 100 
      })
    }
    return null
  }
  
  const validateNotes = (value) => {
    if (value && value.length > 500) {
      return $t('validation.max.string', { 
        attribute: $t('validation.attributes.notes'), 
        max: 500 
      })
    }
    return null
  }
  
  // Hlavní validační funkce
  const validateField = (field, value, allData = {}) => {
    let error = null
    
    switch (field) {
      case 'condition':
        error = validateCondition(value)
        break
      case 'language':
        error = validateLanguage(value)
        break
      case 'quantity':
        error = validateQuantity(value)
        break
      case 'purchase_price':
        error = validatePurchasePrice(value)
        break
      case 'grading':
        error = validateGradeCompany(value)
        break
      case 'grading_cert':
        error = validateGradingCert(value, allData.grading)
        break
      case 'location':
        error = validateLocation(value)
        break
      case 'note':
        error = validateNotes(value)
        break
    }
    
    // Aktualizace chyb
    if (error) {
      errors.value[field] = error
    } else {
      delete errors.value[field]
    }
    
    return error
  }
  
  // Validace všech polí
  const validateAll = (data) => {
    isValidating.value = true
    const newErrors = {}
    
    // Validace jednotlivých polí
    Object.keys(data).forEach(field => {
      const error = validateField(field, data[field], data)
      if (error) {
        newErrors[field] = error
      }
    })
    
    // Cross-field validace pro grading
    if (data.grading && !data.grading_cert) {
      newErrors.grading_cert = $t('validation.custom.grade_value.required_with_company')
    }
    if (data.grading_cert && !data.grading) {
      newErrors.grading = $t('validation.custom.grade_company.required_with_value')
    }
    
    errors.value = newErrors
    isValidating.value = false
    
    return Object.keys(newErrors).length === 0
  }
  
  // Computed pro kontrolu validity
  const isValid = computed(() => Object.keys(errors.value).length === 0)
  const hasErrors = computed(() => Object.keys(errors.value).length > 0)
  
  // Funkce pro vyčištění chyb
  const clearErrors = () => {
    errors.value = {}
  }
  
  const clearError = (field) => {
    delete errors.value[field]
  }
  
  return {
    errors,
    isValidating,
    isValid,
    hasErrors,
    validateField,
    validateAll,
    clearErrors,
    clearError
  }
} 