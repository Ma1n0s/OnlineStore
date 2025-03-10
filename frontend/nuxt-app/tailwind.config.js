import colors from "tailwindcss/colors";
import fluid, { extract, screens, fontSize } from "fluid-tailwind";

export default {
  content: {
    // files: [/* ... */],
    extract,
  },
  plugins: [fluid],
  theme: {
    screens,
    fontSize,
    extend: {
      colors: {
        primary: colors.green,
      },
    },
  },
};
