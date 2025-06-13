<template>
  <GameLayout>
    <div class="flex justify-center items-center min-h-screen">
      <GamePanel color="green" class="w-11/12 max-w-4xl">
        <div class="text-center text-[#00FF00] p-5 animate-scanline">
          <GameTitle title="BATALLA NAVAL" size="xl" />
          
          <div class="w-[100px] h-[50px] mx-auto my-5 bg-no-repeat scale-[2] transform pixel-ship"></div>
          
          <div class="my-8">
            <p class="text-xs leading-loose">PREPÁRATE PARA LA GUERRA NAVAL</p>
            <p class="text-xs leading-loose">HUNDE LA FLOTA ENEMIGA</p>
          </div>
          
          <GameButton size="lg" @click="startGame">INICIAR JUEGO</GameButton>
          
          <!-- Botones de autenticación simplificados -->
          <div class="mt-5 flex justify-center space-x-4">
            <!-- Mostrar cuando NO está autenticado -->
            <div v-if="!isAuthenticated" class="flex space-x-4">
              <button @click="openLoginModal" class="auth-button">
                INICIAR SESIÓN
              </button>
              <button @click="openRegisterModal" class="auth-button">
                REGISTRARSE
              </button>
            </div>
            
            <!-- Mostrar cuando SÍ está autenticado -->
            <div v-else class="flex flex-col items-center">
              <p class="text-xs mb-2">BIENVENIDO, {{ username }}</p>
              <button @click="logout" class="auth-button">
                CERRAR SESIÓN
              </button>
            </div>
          </div>
          
          <div class="mt-10">
            <p class="text-xs leading-relaxed">CÓMO JUGAR:</p>
            <p class="text-xs leading-relaxed">1. COLOCA TUS BARCOS</p>
            <p class="text-xs leading-relaxed">2. TURNA DISPAROS</p>
            <p class="text-xs leading-relaxed">3. HUNDE TODOS LOS BARCOS ENEMIGOS PARA GANAR</p>
          </div>
          
          <div class="mt-10">
            <p class="animate-blink mb-5 text-xs">INSERTA MONEDA PARA CONTINUAR</p>
            <p class="text-[8px] text-[#007700]">© 2023 BATALLA NAVAL RETRO</p>
          </div>
        </div>
      </GamePanel>
    </div>

    <!-- Modal de inicio de sesión MEJORADO -->
    <div v-if="showLoginModal" class="modal-overlay">
      <div class="modal-backdrop" @click="closeModals"></div>
      <div class="modal-container">
        <div class="modal-content">
          <div class="flex justify-between items-center mb-4">
            <GameTitle title="INICIAR SESIÓN" size="md" />
            <button @click="closeModals" class="modal-close">&times;</button>
          </div>
          <form @submit.prevent="login" class="mt-5">
            <div class="mb-4">
              <label class="block text-[#00FF00] text-xs mb-1">EMAIL:</label>
              <input type="email" v-model="loginForm.email" class="modal-input" required>
            </div>
            <div class="mb-6">
              <label class="block text-[#00FF00] text-xs mb-1">CONTRASEÑA:</label>
              <input type="password" v-model="loginForm.password" class="modal-input" required>
            </div>
            <div class="text-center">
              <p v-if="loginError" class="text-red-500 text-xs mb-3">{{ loginError }}</p>
              <GameButton type="submit" size="md" :loading="isLoading" class="glow-effect">ENTRAR</GameButton>
              <p class="text-[#00FF00] text-xs mt-3 cursor-pointer hover:text-white" @click="openRegisterModal">¿NO TIENES CUENTA? REGÍSTRATE</p>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de registro MEJORADO -->
    <div v-if="showRegisterModal" class="modal-overlay">
      <div class="modal-backdrop" @click="closeModals"></div>
      <div class="modal-container">
        <div class="modal-content">
          <div class="flex justify-between items-center mb-4">
            <GameTitle title="CREAR CUENTA" size="md" />
            <button @click="closeModals" class="modal-close">&times;</button>
          </div>
          <form @submit.prevent="register" class="mt-5">
            <div class="mb-3">
              <label class="block text-[#00FF00] text-xs mb-1">NOMBRE:</label>
              <input type="text" v-model="registerForm.name" class="modal-input" required>
            </div>
            <div class="mb-3">
              <label class="block text-[#00FF00] text-xs mb-1">EMAIL:</label>
              <input type="email" v-model="registerForm.email" class="modal-input" required>
            </div>
            <div class="mb-3">
              <label class="block text-[#00FF00] text-xs mb-1">CONTRASEÑA:</label>
              <input type="password" v-model="registerForm.password" class="modal-input" required>
            </div>
            <div class="mb-3">
              <label class="block text-[#00FF00] text-xs mb-1">CONFIRMAR CONTRASEÑA:</label>
              <input type="password" v-model="registerForm.password_confirmation" class="modal-input" required>
            </div>
            <div class="text-center">
              <p v-if="registerError" class="text-red-500 text-xs mb-3">{{ registerError }}</p>
              <GameButton type="submit" size="md" :loading="isLoading">REGISTRARSE</GameButton>
              <p class="text-[#00FF00] text-xs mt-3 cursor-pointer hover:text-white" @click="openLoginModal">¿YA TIENES CUENTA? INICIA SESIÓN</p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </GameLayout>
</template>

<script>
import GameLayout from '@/Components/GameLayout.vue';
import GameTitle from '@/Components/GameTitle.vue';
import GamePanel from '@/Components/GamePanel.vue';
import GameButton from '@/Components/GameButton.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

export default {
  name: 'Welcome',
  components: {
    GameLayout,
    GameTitle,
    GamePanel,
    GameButton
  },
  data() {
    return {
      playerName: '',
      gameReady: false,
      isAuthenticated: false,
      username: '',
      showLoginModal: false,
      showRegisterModal: false,
      isLoading: false,
      loginError: '',
      registerError: '',
      debugMode: false, // Cambiado a false para desactivar modo debug
      loginForm: {
        email: '',
        password: ''
      },
      registerForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  mounted() {
    this.checkAuthStatus();
    console.log('Componente montado. Debug mode:', this.debugMode);
  },
  methods: {
    startGame() {
      console.log('Iniciando juego...');
      router.visit('/inicio');
    },
    openLoginModal() {
      this.closeModals();
      this.showLoginModal = true;
      console.log('Modal de login abierto:', this.showLoginModal);
    },
    openLoginModalDirect() {
      this.closeModals();
      this.showLoginModal = true;
      console.log('Modal de login abierto directamente:', this.showLoginModal);
    },
    openRegisterModal() {
      this.closeModals();
      this.showRegisterModal = true;
      console.log('Modal de registro abierto:', this.showRegisterModal);
    },
    openRegisterModalDirect() {
      this.closeModals();
      this.showRegisterModal = true;
      console.log('Modal de registro abierto directamente:', this.showRegisterModal);
    },
    closeModals() {
      this.showLoginModal = false;
      this.showRegisterModal = false;
      this.loginError = '';
      this.registerError = '';
      console.log('Modales cerrados');
    },
    checkAuthStatus() {
      // Comprobar si hay un token en localStorage
      const token = localStorage.getItem('auth_token');
      const username = localStorage.getItem('username');
      
      if (token && username) {
        this.isAuthenticated = true;
        this.username = username;
      }
    },
    async login() {
      this.isLoading = true;
      this.loginError = '';
      
      try {
        const response = await axios.post('/api/login', this.loginForm);
        localStorage.setItem('auth_token', response.data.token);
        localStorage.setItem('username', response.data.user.name);
        this.isAuthenticated = true;
        this.username = response.data.user.name;
        this.closeModals();
      } catch (error) {
        console.error('Error de inicio de sesión:', error);
        this.loginError = error.response?.data?.message || 'Error al iniciar sesión';
      } finally {
        this.isLoading = false;
      }
    },
    async register() {
      this.isLoading = true;
      this.registerError = '';
      
      try {
        const response = await axios.post('/api/register', this.registerForm);
        localStorage.setItem('auth_token', response.data.token);
        localStorage.setItem('username', response.data.user.name);
        this.isAuthenticated = true;
        this.username = response.data.user.name;
        this.closeModals();
      } catch (error) {
        console.error('Error de registro:', error);
        this.registerError = error.response?.data?.message || 'Error al registrarse';
      } finally {
        this.isLoading = false;
      }
    },
    async logout() {
      this.isLoading = true;
      
      try {
        const token = localStorage.getItem('auth_token');
        await axios.post('/api/logout', {}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
      } finally {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('username');
        this.isAuthenticated = false;
        this.username = '';
        this.isLoading = false;
      }
    },
    showStats() {
      console.log('Mostrando estadísticas del usuario');
      // Implementar posteriormente
    }
  }
}
</script>

<style>
.pixel-ship {
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50" viewBox="0 0 100 50"><rect x="40" y="20" width="60" height="10" fill="%2300FF00"/><rect x="30" y="30" width="60" height="10" fill="%2300FF00"/><rect x="20" y="40" width="70" height="10" fill="%2300FF00"/><rect x="80" y="10" width="10" height="10" fill="%2300FF00"/></svg>');
  image-rendering: pixelated;
}

@keyframes scanline {
  0% { background-position: 0 0; }
  100% { background-position: 0 100%; }
}

.animate-scanline {
  animation: scanline 4s linear infinite;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

.animate-blink {
  animation: blink 1s infinite;
}

.pixel-border {
  box-shadow: 0 0 0 1px #00FF00;
}

/* Nuevo estilo para botones de autenticación */
.auth-button {
  padding: 8px 16px !important;
  background-color: black !important;
  color: #00FF00 !important;
  border: 2px solid #00FF00 !important;
  font-size: 12px !important;
  cursor: pointer !important;
  transition: all 0.2s !important;
  box-shadow: 0 0 8px rgba(0, 255, 0, 0.4) !important;
  text-transform: uppercase !important;
}

.auth-button:hover {
  background-color: #003300 !important;
  box-shadow: 0 0 15px rgba(0, 255, 0, 0.7) !important;
  color: white !important;
}

/* Eliminar estilos de dropdown */
.account-dropdown, .account-dropdown-content, .dropdown-item {
  display: none;
}

/* NUEVOS ESTILOS PARA MODALES - DISEÑO MEJORADO Y MÁS VISIBLE */
.modal-overlay {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: 100% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  z-index: 9999 !important; /* Valor extremadamente alto */
}

.modal-backdrop {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: 100% !important;
  background-color: rgba(0, 0, 0, 0.85) !important;
  z-index: 9998 !important;
}

.modal-container {
  position: relative !important;
  width: 90% !important;
  max-width: 500px !important;
  background-color: black !important;
  border: 3px solid #00FF00 !important;
  box-shadow: 0 0 30px rgba(0, 255, 0, 0.7) !important;
  z-index: 10000 !important;
}

.modal-content {
  padding: 20px !important;
  color: #00FF00 !important;
}

.modal-close {
  font-size: 24px !important;
  color: #00FF00 !important;
  background: none !important;
  border: none !important;
  cursor: pointer !important;
}

.modal-close:hover {
  color: white !important;
}

.modal-input {
  width: 100% !important;
  background-color: black !important;
  border: 2px solid #00FF00 !important;
  color: #00FF00 !important;
  padding: 8px !important;
  font-size: 12px !important;
  margin-bottom: 10px !important;
}

.modal-input:focus {
  outline: none !important;
  box-shadow: 0 0 10px rgba(0, 255, 0, 0.8) !important;
  border-color: white !important;
}

.glow-effect {
  box-shadow: 0 0 10px rgba(0, 255, 0, 0.5), 0 0 20px rgba(0, 255, 0, 0.3);
  transition: all 0.3s ease;
}

.glow-effect:hover {
  box-shadow: 0 0 15px rgba(0, 255, 0, 0.8), 0 0 30px rgba(0, 255, 0, 0.5);
}

/* Add dropdown animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px) translateX(-50%); }
  to { opacity: 1; transform: translateY(0) translateX(-50%); }
}

.account-dropdown {
  animation: fadeIn 0.2s ease-out !important;
}
</style>