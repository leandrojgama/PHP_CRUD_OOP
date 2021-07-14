<?php
//**********************************************************
class Connection{
	private $servername;
	private $username ;
	private $password ;
	private $dbname ;
	private $charset;
	private $connection;
	public function  connect(){
		$this->servername = "localhost";
		$this->username = "root";
		$this->password = "";
		$this->dbname = "mysqlpdo";
		$this->charset = "utf8mb4";
		

		try {
			$dsn = "mysql:host=".$this->servername.";dbname=".$this->dbname.";charset=".$this->charset;
			$this->connection = new PDO($dsn,$this->username,$this->password);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $this->connection;

		}
		catch(PDOException $e)
		{
			echo "Connection failed: " . $e->getMessage();
		}
	}
	public function closeConnection(){
		$this->connection=null;


	}

}

//******************************************************
class Table  {
	private $nome;//string
	private $valores,$colunas,$thColunas;// array
	
	

	function __construct($nome){//include
		$this->nome = $nome;		
	}
	
	public function setValores(array $valores){
		$this->valores=$valores;
	}

	public function setColunas(array $colunas){
		$this->colunas=$colunas;
	}
	public function setThColunas(array $colunas){
		$this->thColunas=$colunas;
	}
	public function  getThColunas(){
		return 
		$this->thColunas;		
	}
	public function setNome($nome){
		$this->nome=$nome;

	}
	public function getNome(){
		
		return $this->nome;
	}

	public function gnome(){
		return $this->nome;
	}

	public function formatTableOpen(){
		$thColunas+="
		<style>
		table{border: solid 1px black;}
		td,th{width: 150px; border: 1px solid black;}

		</style>
		<table> <tr>";

		for($i=0;$i<sizeof($this->thColunas);$i++){			
			$thColunas+="<th>".$this->thColunas($i)."</th>";			
		}
		$thColunas+="</tr>";
		return $thColunas;

	}
	public function formatTd($data,$tagTable){
		switch ($tagTable) {
			case 'td':
			return "<td>".$data."</td>";
			break;
			case 'tr':
			return "<tr><td>".$data."</td>";
			break;
			case '/tr':
			return "<td>".$data."</td></tr>";
			break;
			
			default:
				# code...
			break;
		}

	}


	public function formatTableClose(){
		return "</table>";

	}
	public function  getValores(){
		return $this->valores;
	}
	public function  getColunas(){
		return $this->colunas;
	}
	
	public function  getValoresString(){
		$valores="";
		for($i=0;$i<sizeof($this->valores);$i++){
			if($i==(sizeof($this->valores)-1)||(sizeof($this->valores)==1)){
				$valores.="'".$this->valores[$i]."'";
			}else{
				$valores.="'".$this->valores[$i]."',";
			}
		}
		return $valores;
	}

	public function  getColunasString(){
		$colunas="";
	
		for($i=0;$i<sizeof($this->colunas);$i++){
			if($i==(sizeof($this->colunas)-1)||(sizeof($this->colunas)==1)){
				$colunas.=$this->colunas[$i];
			}else{
				$colunas.=$this->colunas[$i].",";
			}
		}		
		return $colunas;
	}
	
}

//******************************************************

class Database{
	
	public function createDataBase(){
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "mysqlpdo";
		try {
			$conn = new PDO("mysql:host=$servername", $username, $password);
			
			// set the PDO error mode to exception
			// sql to database
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "CREATE DATABASE $dbname";
			$conn->exec($sql);
			echo "Database created successfully<br>";
			
			// sql to create table
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$sql1 = " CREATE TABLE cliente(
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			nome VARCHAR(30) NOT NULL,
			cpf VARCHAR(30) NOT NULL,			
			reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			)";
		  
			// use exec() because no results are returned
			$conn->exec($sql1);
			echo "Table MyGuests created successfully";
		  } catch(PDOException $e) {
			echo $sql . "<br>" . $e->getMessage();
		  }
		  finally {
			$conn = null;
			}



	}

	public function inserir($tabela){
		
		$colunas = $tabela->getColunasString();
		$valores = $tabela->getValoresString();
		$nome = $tabela->getNome();//"'".$tabela->getnome."'";
		//echo "<br>xxx".$nomeTabela;
 /* $sql = "INSERT INTO $nomeTabela (id,$colunas)
			VALUES (null,'test','test')";*/

	try {
			$conexao=new Connection();

			$conn = $conexao->connect();
			
   $sql = "INSERT INTO $nome (id,$colunas)
			VALUES (null,$valores)";
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "New record created successfully";
  }
		catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}
		finally {
			$conn = null;
		}
	}
	/*
	for($i=0;$i<sizeof($this->colunas);$i++){
					echo $this->colunas[$i] . " - ". $row[$this->colunas[$i]] . " | ";
					}
	
	*/
	public function select($tabela){
		$colunas = $tabela->getColunas();
				
		$tb_nome=$tabela->getNome();
		
		
		try{
			$conexao=new Connection();
			$conn = $conexao->connect();
			if($conn){
				$query = "SELECT * FROM $tb_nome";
				$result  = $conn->query($query);
				foreach ($result as $row) {
					echo "* ";
					for($i=0;$i<sizeof($colunas);$i++){
						echo $colunas[$i] . ": ". $row[$colunas[$i]] . " -|- ";
						}
					echo "<br>";
				}
				
			}
			else{
				echo $conn;
			}
		}
		catch(PDOException $ex){
			echo $ex->getMessage();
		}
		finally {
			$conn = null;
		}
	}

	public function delete($tabela,$id){
		$tb_nome=$tabela->getNome();
		
		try {
			$conexao=new Connection();
			$conn = $conexao->connect();


    // sql to delete a record
			$sql = "DELETE FROM $tb_nome WHERE id=$id";

    // use exec() because no results are returned
			$conn->exec($sql);
			echo "Record deleted successfully";
		}
		catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}finally {
			$conn = null;
		}
	}
	//$db->update($table,"nome","gama",15);
	public function update($tabela,$field,$data,$id){
		$tb_nome=$tabela->getNome();
		
		try {

			$conexao=new Connection();
			$conn = $conexao->connect();


			$sql = "UPDATE $tb_nome SET $field ='$data' WHERE id=$id";

    // Prepare statement
			$stmt = $conn->prepare($sql);

    // execute the query
			$stmt->execute();

    // echo a message to say the UPDATE succeeded
			echo $stmt->rowCount() . " records UPDATED successfully";
		}
		catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}finally {
			$conn = null;
		}

	}

}





$db=new Database();

//************Criar o Banco - Db e Tabelas************
//$db->createDataBase();
//************Tabelas - Preparação para inserir************
$table= new Table("cliente");
$field=array("nome","cpf");
$table->setColunas($field);
$valores=array("hoje","5555");
$table->setValores($valores);

//************Teste Insert************
//$db->inserir($table);

//************Teste Select************
//$db->select($table);
//************Teste delete************
//db->delete($table,14);
//************Teste Update************
$db->update($table,"nome","Leandro",17);

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>
	<?php /*


	 $table= new Table();

	 echo "<br>".$table->getNome()."f1<br>";
	echo $table->getNome()."f2<br>";

$field=array("nome","cpf");	 
$table->setColunas($field);
$table->setNome("cliente");   
$valores=array("Leandro","Gama");
$table->setValores($valores);
 $db=new Database();
$db->inserir($table);
*/
	 ?>

</body>
</html>