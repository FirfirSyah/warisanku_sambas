/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/**/*.php",
    "./pages/**/*.php",
    "./admin/**/*.php",
    "./includes/**/*.php",
    "./public/assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        hijau: "#173C30",
        emas: "#D8B66A",
        krem: "#F6F0E3",
        gading: "#FFFAF0",
        marun: "#6F2935",
      },
      fontFamily: {
        serif: ['Fraunces', 'Georgia', 'serif'],
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
