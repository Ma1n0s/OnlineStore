import fluid, { extract, screens, fontSize } from "fluid-tailwind";

export default {
  content: {
    files: ["./src/**/*.{html,js,jsx,ts,tsx,vue}"],
    extract,
  },
  plugins: [fluid],
  theme: {
    screens,
    fontSize,

    extend: {
      colors: {
        primary: {
          DEFAULT: "#89CFF0",
          hover: "#60BBCB",
          active: "#60BBCB",
        },
        second: {
          DEFAULT: "#1B998B",
          hover: "#31918C",
          active: "#31918C",
        },
        white: "#E9F1F7",
        danger: "#D62828",
        dark: "#0D1B2A",
        gray: "#70798C",
      },
    },
  },
};
