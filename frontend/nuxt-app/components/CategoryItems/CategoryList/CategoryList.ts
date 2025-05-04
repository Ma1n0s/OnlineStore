export const getPath = (category, pathPrefix) => {
  if (category.haveProducts) return `/products${pathPrefix}${category.slug}`
  return `${pathPrefix}${category.slug}`
}
