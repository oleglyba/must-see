const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  // Hover styles only apply on devices that actually support hover
  // (wrapped in @media (hover: hover)) so they don't stick on touch.
  future: {
    hoverOnlyWhenSupported: true,
  },
  content: [
    "./*.php",
    "./template-parts/**/*.php",
    "./page-templates/**/*.php",
    "./inc/**/*.php",
    "./assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: "#d4e9ff",
          100: "#a9d2fd",
          300: "#5b9de3",
          500: "#3065cf",
          700: "#244ea0",
          DEFAULT: "#3065cf",
        },
        accent: {
          50: "#fdf3e9",
          200: "#ffdab4",
          500: "#ff8000",
          600: "#e57300",
          DEFAULT: "#ff8000",
        },
        highlight: {
          300: "#ffe76a",
          700: "#9f8603",
          DEFAULT: "#ffe76a",
        },
      },
      fontFamily: {
        sans: ["var(--font-inter)", ...defaultTheme.fontFamily.sans],
        button: ["var(--font-inter)", ...defaultTheme.fontFamily.sans],
      },
      maxWidth: {
        "6xl": "72rem",
      },
    },
  },
  plugins: [],
};
