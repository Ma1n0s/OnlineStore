export const getBreadcrumbs = (slug: string[]) => {
  const breadcrumbs = []
  slug.forEach(item => {
    breadcrumbs.push({
      name: item,
      url: `/category/${item}`,
    })
  })
  return breadcrumbs
}

export const getBreadcrumbsFromCategoryPath = (categoryPath: any[]) => {
  const breadcrumbs = []
  categoryPath.forEach(item => {
    breadcrumbs.push({
      name: item.name,
      url: `/category/${item.slug}`,
    })
  })
  return breadcrumbs
}
