import type { Img } from './shared.types'

export type ProductPrice = {
  sum: number | string
  discount: number | string
  total: number | string
}

export type Category = {
  id: number | string
  name: string
  slug: string
}

export type Subcategory = {
  id: number | string
  name: string
  slug: string
}

export type Specification = {
  [key: string]: {
    [key: string]: string
  }
}

export type Product = {
  id: number | string
  code?: string | number
  name: string
  slug: string
  description: string | null
  price: Record<string, any>
  article: string | number
  brand: string
  raiting: number
  category: Category
  subcategory: Subcategory
  specifications: Specification
  mainSpecifications: Specification
  shortDescription: string | null
  images: Img[]
  created_at: string
  updated_at: string
  reviewsCount: number
  questionsCount: number
  warranty: string
  advantages: Record<string, any>
  specificationsВ?: Record<string, any>
}
