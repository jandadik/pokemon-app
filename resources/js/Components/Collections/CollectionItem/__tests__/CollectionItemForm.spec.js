import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import { nextTick } from 'vue'
import CollectionItemForm from '../CollectionItemForm.vue'

// Mock composables
vi.mock('@/composables/useCardUtils', () => ({
  getCardImageUrl: vi.fn(() => 'test-image-url'),
  handleImageError: vi.fn()
}))

// Mock useForm
const mockForm = {
  data: vi.fn(() => ({
    condition: 'near_mint',
    language: 'english',
    quantity: 1,
    purchase_price: '',
    grading: '',
    grading_cert: '',
    first_edition: false,
    location: '',
    note: ''
  })),
  errors: {},
  processing: false
}

// Mock i18n
const mockT = vi.fn((key, params) => {
  const translations = {
    'collections.form.title.create': 'Přidat položku',
    'collections.form.title.edit': 'Upravit položku',
    'collections.form.save': 'Uložit',
    'collections.form.cancel': 'Zrušit',
    'validation.required': `Pole ${params?.attribute || 'field'} je povinné`,
    'validation.custom.condition.invalid': 'Neplatný stav karty',
    'validation.custom.language.invalid': 'Neplatný jazyk',
    'validation.between.numeric': `Pole ${params?.attribute || 'field'} musí být mezi ${params?.min} a ${params?.max}`,
    'validation.attributes.condition': 'stav',
    'validation.attributes.language': 'jazyk',
    'validation.attributes.quantity': 'množství'
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

describe('CollectionItemForm', () => {
  let vuetify
  let wrapper

  const defaultProps = {
    card: {
      id: 'test-card-id',
      name: 'Test Card',
      img_file_small: 'test.jpg'
    },
    variant: {
      id: 'test-variant-id',
      name: 'Test Variant'
    },
    form: mockForm,
    mode: 'create'
  }

  beforeEach(() => {
    vuetify = createVuetify()
    vi.clearAllMocks()
  })

  afterEach(() => {
    if (wrapper) {
      wrapper.unmount()
    }
  })

  const createWrapper = (props = {}) => {
    return mount(CollectionItemForm, {
      props: { ...defaultProps, ...props },
      global: {
        plugins: [vuetify],
        stubs: {
          ConditionSelect: {
            template: '<div data-testid="condition-select"><input v-model="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" /></div>',
            props: ['modelValue', 'errorMessages'],
            emits: ['update:modelValue']
          },
          LanguageSelect: {
            template: '<div data-testid="language-select"><input v-model="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" /></div>',
            props: ['modelValue', 'errorMessages'],
            emits: ['update:modelValue']
          },
          QuantityInput: {
            template: '<div data-testid="quantity-input"><input type="number" v-model="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" /></div>',
            props: ['modelValue', 'errorMessages'],
            emits: ['update:modelValue']
          },
          PriceInput: true,
          GradingSection: true,
          FirstEditionCheckbox: true,
          LocationInput: true,
          NoteInput: true
        }
      }
    })
  }

  describe('Rendering', () => {
    it('renders create form correctly', () => {
      wrapper = createWrapper()
      
      expect(wrapper.find('[data-testid="condition-select"]').exists()).toBe(true)
      expect(wrapper.find('[data-testid="language-select"]').exists()).toBe(true)
      expect(wrapper.find('[data-testid="quantity-input"]').exists()).toBe(true)
    })

    it('renders edit form correctly', () => {
      wrapper = createWrapper({ mode: 'edit' })
      
      // V edit režimu by měl být zobrazen náhled karty
      expect(wrapper.text()).toContain('Test Card')
    })
  })

  describe('Frontend Validation', () => {
    it('validates condition field', async () => {
      wrapper = createWrapper()
      
      // Najdeme condition select a změníme hodnotu na neplatnou
      const conditionInput = wrapper.find('[data-testid="condition-select"] input')
      await conditionInput.setValue('invalid_condition')
      await conditionInput.trigger('input')
      
      // Počkáme na validaci (setTimeout 300ms)
      await new Promise(resolve => setTimeout(resolve, 350))
      await nextTick()
      
      // Zkontrolujeme, že se zobrazila chyba
      expect(mockT).toHaveBeenCalledWith('validation.custom.condition.invalid')
    })

    it('validates language field', async () => {
      wrapper = createWrapper()
      
      const languageInput = wrapper.find('[data-testid="language-select"] input')
      await languageInput.setValue('invalid_language')
      await languageInput.trigger('input')
      
      await new Promise(resolve => setTimeout(resolve, 350))
      await nextTick()
      
      expect(mockT).toHaveBeenCalledWith('validation.custom.language.invalid')
    })

    it('validates quantity field', async () => {
      wrapper = createWrapper()
      
      const quantityInput = wrapper.find('[data-testid="quantity-input"] input')
      await quantityInput.setValue('0') // Neplatná hodnota (min je 1)
      await quantityInput.trigger('input')
      
      await new Promise(resolve => setTimeout(resolve, 350))
      await nextTick()
      
      expect(mockT).toHaveBeenCalledWith('validation.between.numeric', {
        attribute: 'množství',
        min: 1,
        max: 999
      })
    })

    it('validates required fields', async () => {
      wrapper = createWrapper()
      
      const conditionInput = wrapper.find('[data-testid="condition-select"] input')
      await conditionInput.setValue('')
      await conditionInput.trigger('input')
      
      await new Promise(resolve => setTimeout(resolve, 350))
      await nextTick()
      
      expect(mockT).toHaveBeenCalledWith('validation.required', {
        attribute: 'stav'
      })
    })
  })

  describe('Events', () => {
    it('emits submit event when save button is clicked', async () => {
      wrapper = createWrapper()
      
      const saveButton = wrapper.find('button')
      await saveButton.trigger('click')
      
      expect(wrapper.emitted('submit')).toBeTruthy()
    })

    it('emits cancel event when cancel button is clicked', async () => {
      wrapper = createWrapper()
      
      const buttons = wrapper.findAll('button')
      const cancelButton = buttons[1] // Druhé tlačítko by mělo být Cancel
      await cancelButton.trigger('click')
      
      expect(wrapper.emitted('cancel')).toBeTruthy()
    })
  })

  describe('Error Display', () => {
    it('combines frontend and backend errors correctly', () => {
      const formWithErrors = {
        ...mockForm,
        errors: {
          condition: 'Backend error for condition'
        }
      }

      wrapper = createWrapper({ form: formWithErrors })
      
      // Backend chyba by měla mít přednost pokud neexistuje frontend chyba
      const vm = wrapper.vm
      expect(vm.getFieldErrors('condition')).toBe('Backend error for condition')
    })

    it('prioritizes frontend errors over backend errors', async () => {
      const formWithErrors = {
        ...mockForm,
        errors: {
          condition: 'Backend error for condition'
        }
      }

      wrapper = createWrapper({ form: formWithErrors })
      
      // Vyvoláme frontend chybu
      const conditionInput = wrapper.find('[data-testid="condition-select"] input')
      await conditionInput.setValue('invalid_condition')
      await conditionInput.trigger('input')
      
      await new Promise(resolve => setTimeout(resolve, 350))
      await nextTick()
      
      // Frontend chyba by měla mít přednost
      const vm = wrapper.vm
      expect(vm.getFieldErrors('condition')).toContain('Neplatný stav karty')
    })
  })
}) 