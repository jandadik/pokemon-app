import { describe, it, expect, beforeEach, vi } from 'vitest'
import { ref } from 'vue'
import { useCollectionItemValidation } from '../useCollectionItemValidation'

// Mock i18n
const mockT = vi.fn((key, params) => {
  const translations = {
    'validation.required': `Pole ${params?.attribute || 'field'} je povinné`,
    'validation.custom.condition.invalid': 'Neplatný stav karty',
    'validation.custom.language.invalid': 'Neplatný jazyk',
    'validation.custom.grade_company.invalid': 'Neplatná grading agentura',
    'validation.custom.grade_value.required_with_company': 'Certifikát je povinný při výběru agentury',
    'validation.custom.grade_company.required_with_value': 'Agentura je povinná při zadání certifikátu',
    'validation.between.numeric': `Pole ${params?.attribute || 'field'} musí být mezi ${params?.min} a ${params?.max}`,
    'validation.min.numeric': `Pole ${params?.attribute || 'field'} musí být minimálně ${params?.min}`,
    'validation.max.string': `Pole ${params?.attribute || 'field'} může mít maximálně ${params?.max} znaků`,
    'validation.attributes.condition': 'stav',
    'validation.attributes.language': 'jazyk',
    'validation.attributes.quantity': 'množství',
    'validation.attributes.purchase_price': 'nákupní cena',
    'validation.attributes.location': 'umístění',
    'validation.attributes.notes': 'poznámka',
    'validation.attributes.grading_cert': 'certifikát'
  }
  return translations[key] || key
})

// Mock getCurrentInstance
vi.mock('vue', async () => {
  const actual = await vi.importActual('vue')
  return {
    ...actual,
    getCurrentInstance: () => ({
      appContext: {
        config: {
          globalProperties: {
            $t: mockT
          }
        }
      }
    })
  }
})

describe('useCollectionItemValidation', () => {
  let formData
  let validation

  beforeEach(() => {
    formData = ref({
      condition: 'near_mint',
      language: 'english',
      quantity: 1,
      purchase_price: '',
      grading: '',
      grading_cert: '',
      first_edition: false,
      location: '',
      note: ''
    })
    
    validation = useCollectionItemValidation(formData)
    vi.clearAllMocks()
  })

  describe('Condition Validation', () => {
    it('validates valid condition', () => {
      const error = validation.validateField('condition', 'near_mint')
      expect(error).toBeNull()
      expect(validation.errors.value.condition).toBeUndefined()
    })

    it('rejects invalid condition', () => {
      const error = validation.validateField('condition', 'invalid_condition')
      expect(error).toBe('Neplatný stav karty')
      expect(validation.errors.value.condition).toBe('Neplatný stav karty')
    })

    it('requires condition', () => {
      const error = validation.validateField('condition', '')
      expect(error).toBe('Pole stav je povinné')
      expect(validation.errors.value.condition).toBe('Pole stav je povinné')
    })

    it('accepts all valid conditions', () => {
      const validConditions = ['mint', 'near_mint', 'excellent', 'good', 'played', 'poor']
      
      validConditions.forEach(condition => {
        const error = validation.validateField('condition', condition)
        expect(error).toBeNull()
      })
    })
  })

  describe('Language Validation', () => {
    it('validates valid language', () => {
      const error = validation.validateField('language', 'english')
      expect(error).toBeNull()
    })

    it('rejects invalid language', () => {
      const error = validation.validateField('language', 'invalid_language')
      expect(error).toBe('Neplatný jazyk')
    })

    it('requires language', () => {
      const error = validation.validateField('language', '')
      expect(error).toBe('Pole jazyk je povinné')
    })

    it('accepts all valid languages', () => {
      const validLanguages = ['english', 'german', 'french', 'czech', 'japanese', 'spanish', 'italian', 'portuguese', 'chinese', 'korean']
      
      validLanguages.forEach(language => {
        const error = validation.validateField('language', language)
        expect(error).toBeNull()
      })
    })
  })

  describe('Quantity Validation', () => {
    it('validates valid quantity', () => {
      const error = validation.validateField('quantity', '5')
      expect(error).toBeNull()
    })

    it('rejects quantity below minimum', () => {
      const error = validation.validateField('quantity', '0')
      expect(error).toBe('Pole množství musí být mezi 1 a 999')
    })

    it('rejects quantity above maximum', () => {
      const error = validation.validateField('quantity', '1000')
      expect(error).toBe('Pole množství musí být mezi 1 a 999')
    })

    it('rejects non-numeric quantity', () => {
      const error = validation.validateField('quantity', 'abc')
      expect(error).toBe('Pole množství musí být mezi 1 a 999')
    })

    it('requires quantity', () => {
      const error = validation.validateField('quantity', '')
      expect(error).toBe('Pole množství je povinné')
    })
  })

  describe('Purchase Price Validation', () => {
    it('validates valid price', () => {
      const error = validation.validateField('purchase_price', '10.50')
      expect(error).toBeNull()
    })

    it('allows empty price', () => {
      const error = validation.validateField('purchase_price', '')
      expect(error).toBeNull()
    })

    it('rejects negative price', () => {
      const error = validation.validateField('purchase_price', '-5')
      expect(error).toBe('Pole nákupní cena musí být minimálně 0')
    })

    it('rejects non-numeric price', () => {
      const error = validation.validateField('purchase_price', 'abc')
      expect(error).toBe('Pole nákupní cena musí být minimálně 0')
    })
  })

  describe('Grading Validation', () => {
    it('validates valid grading company', () => {
      const error = validation.validateField('grading', 'psa')
      expect(error).toBeNull()
    })

    it('allows empty grading company', () => {
      const error = validation.validateField('grading', '')
      expect(error).toBeNull()
    })

    it('rejects invalid grading company', () => {
      const error = validation.validateField('grading', 'invalid_company')
      expect(error).toBe('Neplatná grading agentura')
    })

    it('accepts all valid grading companies', () => {
      const validCompanies = ['psa', 'bgs', 'cgc', 'sgc']
      
      validCompanies.forEach(company => {
        const error = validation.validateField('grading', company)
        expect(error).toBeNull()
      })
    })
  })

  describe('Grading Certificate Validation', () => {
    it('validates certificate with company', () => {
      const error = validation.validateField('grading_cert', 'ABC123', { grading: 'psa' })
      expect(error).toBeNull()
    })

    it('allows empty certificate without company', () => {
      const error = validation.validateField('grading_cert', '', { grading: '' })
      expect(error).toBeNull()
    })

    it('rejects long certificate', () => {
      const longCert = 'a'.repeat(51)
      const error = validation.validateField('grading_cert', longCert)
      expect(error).toBe('Pole certifikát může mít maximálně 50 znaků')
    })
  })

  describe('Location Validation', () => {
    it('validates valid location', () => {
      const error = validation.validateField('location', 'Box 1')
      expect(error).toBeNull()
    })

    it('allows empty location', () => {
      const error = validation.validateField('location', '')
      expect(error).toBeNull()
    })

    it('rejects location too long', () => {
      const longLocation = 'a'.repeat(101)
      const error = validation.validateField('location', longLocation)
      expect(error).toBe('Pole umístění může mít maximálně 100 znaků')
    })
  })

  describe('Note Validation', () => {
    it('validates valid note', () => {
      const error = validation.validateField('note', 'Test note')
      expect(error).toBeNull()
    })

    it('allows empty note', () => {
      const error = validation.validateField('note', '')
      expect(error).toBeNull()
    })

    it('rejects note too long', () => {
      const longNote = 'a'.repeat(501)
      const error = validation.validateField('note', longNote)
      expect(error).toBe('Pole poznámka může mít maximálně 500 znaků')
    })
  })

  describe('Cross-field Validation', () => {
    it('validates complete form successfully', () => {
      const validData = {
        condition: 'near_mint',
        language: 'english',
        quantity: 1,
        purchase_price: '10.00',
        grading: '',
        grading_cert: '',
        first_edition: false,
        location: 'Box 1',
        note: 'Test note'
      }

      const isValid = validation.validateAll(validData)
      expect(isValid).toBe(true)
      expect(Object.keys(validation.errors.value)).toHaveLength(0)
    })

    it('detects multiple validation errors', () => {
      const invalidData = {
        condition: 'invalid_condition',
        language: 'invalid_language',
        quantity: 0,
        purchase_price: '-5',
        grading: 'invalid_company',
        grading_cert: '',
        first_edition: false,
        location: '',
        note: ''
      }

      const isValid = validation.validateAll(invalidData)
      expect(isValid).toBe(false)
      expect(Object.keys(validation.errors.value).length).toBeGreaterThan(0)
    })

    it('validates grading cross-field requirements', () => {
      // Grading company bez certifikátu
      let data = {
        condition: 'near_mint',
        language: 'english',
        quantity: 1,
        grading: 'psa',
        grading_cert: ''
      }

      let isValid = validation.validateAll(data)
      expect(isValid).toBe(false)
      expect(validation.errors.value.grading_cert).toBe('Certifikát je povinný při výběru agentury')

      // Certifikát bez grading company
      data = {
        condition: 'near_mint',
        language: 'english',
        quantity: 1,
        grading: '',
        grading_cert: 'ABC123'
      }

      validation.clearErrors()
      isValid = validation.validateAll(data)
      expect(isValid).toBe(false)
      expect(validation.errors.value.grading).toBe('Agentura je povinná při zadání certifikátu')
    })
  })

  describe('Utility Functions', () => {
    it('clears all errors', () => {
      validation.errors.value = { condition: 'Some error', language: 'Another error' }
      validation.clearErrors()
      expect(Object.keys(validation.errors.value)).toHaveLength(0)
    })

    it('clears specific error', () => {
      validation.errors.value = { condition: 'Some error', language: 'Another error' }
      validation.clearError('condition')
      expect(validation.errors.value.condition).toBeUndefined()
      expect(validation.errors.value.language).toBe('Another error')
    })

    it('computes isValid correctly', () => {
      expect(validation.isValid.value).toBe(true)
      
      validation.errors.value = { condition: 'Some error' }
      expect(validation.isValid.value).toBe(false)
    })

    it('computes hasErrors correctly', () => {
      expect(validation.hasErrors.value).toBe(false)
      
      validation.errors.value = { condition: 'Some error' }
      expect(validation.hasErrors.value).toBe(true)
    })
  })
}) 