export const getBreadcrumbs = (slug: string[]) => {
  const breadcrumbs = []
  let url = ''
  slug.forEach(item => {
    url += `/${item.slug}`
    breadcrumbs.push({
      name: item.name,
      url: `/category${url}`,
    })
  })
  return breadcrumbs
}

export const getBreadcrumbsFromCategoryPath = (categoryPath: any[]) => {
  const breadcrumbs = []
  let url = ''
  categoryPath.forEach(item => {
    url += `/${item.slug}`
    breadcrumbs.push({
      name: item.name,
      url: `/category${url}`,
    })
  })

  breadcrumbs.at(-1).url = `/products/category${url}`

  return breadcrumbs
}
