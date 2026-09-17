package com.saep.backend.Controller;

import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.saep.backend.Model.Usuario;
import com.saep.backend.Repository.UsuarioRepository;

@RestController
@RequestMapping("/api/auth")
@CrossOrigin("*") // Libertar as requisições do angular (TS)
public class AuthController {
    private final UsuarioRepository usuarioRepository;
    AuthController(UsuarioRepository usuarioRepository) {
        this.usuarioRepository = usuarioRepository;
    }

    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody Usuario loginData){
        return usuarioRepository.findByLoginAndSenha(loginData.getLogin(), loginData.getSenha()) // Passa o login e a senha para a api
            .map(usuario -> ResponseEntity.ok(usuario)) // Se estiver certo retorna o usuário e o status 200
            .orElseGet(()-> ResponseEntity.status(401).build()); // Caso contrário retorna o erro 401 (não Autorizado)
    }
    
}
