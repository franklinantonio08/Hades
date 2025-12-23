<?php

public function postNuevo(){
//    return $this->request->all();
    $departamentoExiste = Departamento::where('nombre', $this->request->nombre)
    //->where('distribuidorId', Auth::user()->distribuidorId)
    ->first();
    if(!empty($departamentoExiste)){
        return redirect('dist/departamento/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
    }
    DB::beginTransaction();
    try { 	
        $departamento = new Departamento;
        $departamento->nombre         = trim($this->request->nombre);
        if(isset($this->request->comentario)){
            $departamento->infoextra       = trim($this->request->comentario); 
        }
        $departamento->estatus          = 'Activo';
        $departamento->created_at       = date('Y-m-d H:i:s');
        $departamento->usuarioId        = Auth::user()->id;
        $departamento->organizacionId = 1;
        $result = $departamento->save();

        $departamentoId = $departamento->id;

        if(empty($departamentoId)){
            DB::rollBack();
            return redirect('dist/departamento/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
        }
        
        $departamentoCode = str_pad($departamentoId,5, "0",STR_PAD_LEFT);
        //return $departamentoCode;
        $departamentoUpdate = Departamento::find($departamentoId);
        $departamentoUpdate->codigo = $departamentoCode;
        $result = $departamentoUpdate->save();	

    } catch(\Illuminate\Database\QueryException $ex){ 
        DB::rollBack();
        return redirect('dist/departamento/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
    }
    
    if($result != 1){
        DB::rollBack();
        return redirect('dist/departamento/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
    }
    DB::commit();

    return redirect('dist/departamento')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
}





curl -vk -X POST https://acrux.migracion.gob.pa/api/login \  -H "Content-Type: application/json" \  -d "{\"username\":\"INVALID\", \"password\":\"INVALID\"}"


curl -vk -X POST "https://acrux.migracion.gob.pa/api/login" \  -H "Content-Type: application/json" \  -d "{\"username\":\"frrodriguez\", \"password\":\"Migracion.2024\"}"



ldapwhoami -x -H ldap://migracion.gob.pa:389 \ -D "frrodriguez@migracion.gob.pa" \ -w "Migracion.2024" \  -v




ldapsearch -H "ldap://migracion.gob.pa:389" \ -D "SNM-LDPA-DS@migracion.gob.pa" \ -W \ -b "DC=migracion,DC=gob,DC=pa" \ "(samAccountName=frrodriguez)" \ cn mail sAMAccountName userPrincipalName



ldapsearch -x \ -H "ldap://SNMDC03.migracion.gob.pa:389" \ -D "SNM-LDPA-DS@migracion.gob.pa" \ -W \ -b "DC=migracion,DC=gob,DC=pa" \ "(samAccountName=frrodriguez)" \ cn mail sAMAccountName userPrincipalName


ldapsearch -x \ -H "ldap://<IP_DEL_CONTROLADOR>:389" \ -D "SNM-LDPA-DS@migracion.gob.pa" \ -W \ -b "DC=migracion,DC=gob,DC=pa" \ "(samAccountName=frrodriguez)" \ cn mail sAMAccountName userPrincipalName


estamos intentando mantener los marcos de los paños fijos en las ventanas grandes 
y cambiar los las ventanas tipo miami abatible hacia fuera como me lo comento iracir 
las puertas de baño se cotizo los dos paños 
los de la media pared y el abatible 
si solo quieres uno el precio puede bajar 
la cotizacion es en base a lo que nos suministro iracir 
si necesitas las fotos yo te las puedo enviar 

48
67
74
75
86
87




carta de responbilidad 
notariadad 

copia de la escritura 
recibo de luz notaria 
copia de la cedula 
