import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { RegisterComponent } from './components/register/register.component';
import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';
import { EditUserProfileComponent } from './components/edit-user-profile/edit-user-profile.component';
import { ErrorComponent } from './components/error/error.component';

const routes: Routes = [
  {path:'', component: HomeComponent},
  {path:'inicio', component:HomeComponent},
  {path:'registro', component:RegisterComponent},
  {path:'login', component:LoginComponent},
  {path:'logout/:sure', component:LoginComponent},
  {path:'user/editar-perfil', component: EditUserProfileComponent},
  {path:'**', component:ErrorComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
