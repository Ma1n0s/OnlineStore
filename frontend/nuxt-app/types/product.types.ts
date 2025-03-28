import type { Img } from "./shared.types";

export type ProductPrice = {
  sum: number | string;
  discount: number | string;
  total: number | string;
};

export type Product = {
  name: string;
  price: ProductPrice;
  article: string | number;
  brand: string;
  raiting: number;
  images: Img[];
};
