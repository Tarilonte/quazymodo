module.exports = {
  content: [
    "./app/**/*",
    "./data/**/*",
    "./core/**/*",
  ],
  theme: {
    extend: {
      colors: {
        tomato:'tomato',
      },
      backdropBlur: { xs: '2px' },
      boxShadow: {
        'navbar': '0 10px 40px -5px rgba(0, 0, 0, 0.3), 0 25px 40px -12px rgba(0, 0, 0, 0.3)',
      },
      fontFamily: {
        sans: ["Noto Sans", "sans-serif"],
        rubik: ["Rubik", "sans-serif"],
        cutive: ["Cutive", "sans-serif"],
        zilla: ["Zilla Slab", "serif"],
        heading: ['Zilla Slab', 'serif'],
      },
      typography: {
        DEFAULT: {
          // As configurações abaixo extendem a configuração padrão do typography: https://github.com/tailwindlabs/tailwindcss-typography/blob/master/src/styles.js
          css: {
            maxWidth: '70ch',
            h1: {fontWeight: "600" },
            h2: {fontWeight: "600" },
          }
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require("daisyui")
  ],
  daisyui: {
    themes: [
      {
        light: {
          ...require("daisyui/src/theming/themes")["cupcake"],
        },
      },
      {
        dark: {
          ...require("daisyui/src/theming/themes")["sunset"],
        },
      },
    ],
  },
}