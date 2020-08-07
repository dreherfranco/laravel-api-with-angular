import { Injectable } from '@angular/core';
import { url } from './global'; 
import { Observable } from 'rxjs';
import { User } from '../models/user';
import { HttpClient, HttpHeaders } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})
export class UserService {

  private url:string;
  public identity;
  public token:string;

  constructor(private _http: HttpClient) {
    this.url = url;
  }

  register(user:User): Observable<any>{

    let params = 'json='+ JSON.stringify(user);
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

    return this._http.post(this.url + '/register', params, { headers: headers });
  }

  login(user, getToken = null): Observable<any>{

    if(getToken == true){
      user.gettoken = 'true';
    }

    let params = 'json='+ JSON.stringify(user);
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

    return this._http.post(this.url + '/login', params, { headers: headers });
  }

  update(user: User, token: string): Observable<any>{
    let params = 'json=' + JSON.stringify(user);
    let headers = new HttpHeaders().set('Authorization', token)
                                   .set('Content-Type', 'application/x-www-form-urlencoded');
    
    return this._http.put(this.url + '/user/update', params, {headers: headers});
  }

  getIdentity(){
    let identity = JSON.parse(localStorage.getItem('identity'));
    
    if(identity && identity != 'undefined')
      this.identity = identity; 
    else
      this.identity = null;
     return this.identity;
  }
  
  getToken():string{
    this.token = localStorage.getItem('token');
    return this.token;
  }

  isAuthenticated():boolean{
    let identity = localStorage.getItem('identity');
    if(identity != null)
      return true;
    else
      return false;
    
  }
}
