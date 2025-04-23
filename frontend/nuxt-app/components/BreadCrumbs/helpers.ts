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
