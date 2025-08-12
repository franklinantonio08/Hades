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