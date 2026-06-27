import { defineStore } from 'pinia'
import { API_URL } from '../config/api.js'
export const useAuthStore = defineStore('auth', {
  state: () => ({
    logueado: false,
    usuario: null,
  }),

  actions: {
    async login(email, password) {
      try {
        const res = await fetch(`${API_URL}/api/auth/login.php`, {
          method: 'POST',
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email,
            password,
          }),
        })
        const text = await res.text()
        let data

        try {
          data = JSON.parse(text)
        } catch (e) {
          console.error('JSON INVÁLIDO')

          return {
            ok: false,
            message: 'El servidor no devuelve JSON válido',
          }
        }

        if (data.ok) {
          this.logueado = true
          this.usuario = data.usuario

          return {
            ok: true,
          }
        } else {
          return {
            ok: false,
            message: data.message,
          }
        }
      } catch (error) {
        console.error(error)

        return {
          ok: false,
          message: 'Error de conexión',
        }
      }
    },
    async comprobarSesion() {
      try {
        const res = await fetch(`${API_URL}/api/auth/comprobarSesion.php`, {
          credentials: 'include',
        })
        const data = await res.json()

        this.logueado = data.logueado

        if (data.usuario) {
          this.usuario = data.usuario
        }
      } catch (error) {
        this.logueado = false
        this.usuario = null
      }
    },
  },
})
