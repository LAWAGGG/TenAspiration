export function setToken(token){
    localStorage.setItem("token", token)
}

export const Token = localStorage.getItem("token")