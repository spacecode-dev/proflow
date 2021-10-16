import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'

/* Middlewares */
import Auth from '@/middlewares/auth'

Vue.use(VueRouter)

/* Auth routes */
const AuthLayout = () => import(/* webpackChunkName: "auth" */ '@/layouts/Auth')
const Login = () => import(/* webpackChunkName: "auth" */ '@/views/auth/Login')
const CompanyDetail = () => import(/* webpackChunkName: "auth" */ '@/views/auth/CompanyDetail')
const PersonalDetail = () => import(/* webpackChunkName: "auth" */ '@/views/auth/PersonalDetail')
const InviteUser = () => import(/* webpackChunkName: "auth" */ '@/views/auth/InviteUser')
const SignUp = () => import(/* webpackChunkName: "auth" */ '@/views/auth/SignUp')
const VerifyEmail = () => import(/* webpackChunkName: "auth" */ '@/views/auth/VerifyEmail')
const ForgotPassword = () => import(/* webpackChunkName: "auth" */ '@/views/auth/ForgotPassword')
const ResetPassword = () => import(/* webpackChunkName: "auth" */ '@/views/auth/ResetPassword')

/* Dashboard routes */
const DashboardLayout = () => import(/* webpackChunkName: "home" */ '@/layouts/Dashboard')
const Home = () => import(/* webpackChunkName: "home" */ '@/views/dashboard/Home.vue')

/* Issue routes */
const IssueView = () => import(/* webpackChunkName: "settings" */ '@/views/issue/IssueView')

const routes = [
  {
    path: '/',
    component: AuthLayout,
    children: [
      {
        path: '',
        name: 'login',
        component: Login,
        beforeEnter: Auth.isGuest
      },
      {
        path: '/sign-up',
        name: 'sign-up',
        component: SignUp,
        beforeEnter: Auth.isGuest
      },
      {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword,
        beforeEnter: Auth.isGuest
      },
      {
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPassword,
        beforeEnter: Auth.isGuest
      },
      {
        path: '/verify-email/:email',
        name: 'verify-email',
        component: VerifyEmail,
        beforeEnter: Auth.isGuest,
        props: true
      },
      {
        path: '/oauth/:token',
        name: 'oauth',
        beforeEnter: async (to, from, next) => {
          if (Auth.isGuest && to.params.token) {
            await store.dispatch('getUserDetailByToken', to.params.token)
            next({ name: 'company-detail' })
          } else {
            next({ name: 'home' })
          }
        },
        props: true
      },
      {
        path: '/company-detail',
        name: 'company-detail',
        component: CompanyDetail,
        beforeEnter: Auth.checkStep
      },
      {
        path: '/personal-detail',
        name: 'personal-detail',
        component: PersonalDetail,
        beforeEnter: Auth.checkStep
      },
      {
        path: '/invite-user',
        name: 'invite-user',
        component: InviteUser,
        beforeEnter: Auth.checkStep
      }
    ]
  },
  {
    path: '/home',
    beforeEnter: Auth.isLoggedIn,
    component: DashboardLayout,
    children: [
      {
        path: '',
        name: 'home',
        component: Home,
        meta: {
          title: 'My Issues'
        }
      },
      {
        path: ':type',
        name: 'home-type',
        component: Home
      },
      {
        path: 'create-view',
        name: 'create-view',
        component: IssueView
      },
      {
        path: 'issue-view/:id',
        name: 'issue-view',
        component: IssueView,
        props: true,
        meta: {
          title: 'Create new Issue'
        }
      },
      {
        path: ':type/:groupId',
        name: 'home-group',
        component: Home
      },
    ]
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
