<?
/*
#define MAX_OSPATH 260
typedef struct demoheader_s
{
  char szFileStamp[6];
  int nDemoProtocol;
  int nNetProtocolVersion;
  char szMapName[MAX_OSPATH];
  char szDllDir[MAX_OSPATH];
  CRC32_t mapCRC;
  int nDirectoryOffset;
}demoheader_t; //sizeof=544

private $szFileStamp_Offset=0;
private $nDemoProtocol_Offset=8;
private $nNetProtocolVersion_Offset=12;
private $szMapName_Offset=16;
private $szDllDir_Offset=276;
private $mapCRC_Offset=536;
private $nDirectoryOffset_Offset=540;

typedef struct demodirectory_t
{
  int nEntries;
  demoentry_t *p_rgEntries;
}demodirectory_s;

typedef struct demoentry_s
{
   int nEntryType;
   char szDescription[64];
   int nFlags;
   int nCDTrack;
   float fTrackTime;
   int nFrames;
   int nOffset;
   int nFileLength;
}demoentry_t; //sizeof = 92

private $nEntryType_Offset=0;
private $szDescription_Offset=4;
private $nFlags_Offset=68;
private $nCDTrack_Offset=72;
private $fTrackTime_Offset=76;
private $nFrames_Offset=80;
private $nOffset_Offset=84;
private $nFileLength_Offset=88;
*/
?>
<?
function SpewError($str)
{
	echo "Error:".$str."<br>\n";
}

function swapEndianness($hex) 
{
    return implode('', array_reverse(str_split($hex, 2)));
}
function ReadInt($File,$Offset)
{
	if(fseek($File,$Offset)==-1)
	{
		return FALSE;
	}
	$Data=unpack("i",fread($File,4));
	return $Data[1];
}
function ReadUint($File,$Offset)
{
	if(fseek($File,$Offset)==-1)
	{
		return FALSE;
	}
	$Data=unpack("I",fread($File,4));
	return $Data[1];
}

function ReadFloat($File,$Offset)
{
	
	if(fseek($File,$Offset)==-1)
	{
		return FALSE;
	}
	$Data=unpack("f",fread($File,4));
	return $Data[1];
}

function ReadData($File,$Offset,$Len)
{
	if(fseek($File,$Offset)==-1)
	{
		return FALSE;
	}

	return fread($File,$Len);
}

function PrintPos($File)
{
	echo "Ftell=".ftell($File)."<br>";
}
class demoheader_t
{
	public $szFileStamp;
	public $nDemoProtocol;
	public $nNetProtocolVersion;
	public $szMapName;
	public $szDllDir;
	public $mapCRC;
	public $nDirectoryOffset;
	
    private $szFileStamp_Offset=0;
    private $nDemoProtocol_Offset=8;
	private $nNetProtocolVersion_Offset=12;
	private $szMapName_Offset=16;
	private $szDllDir_Offset=276;
	private $mapCRC_Offset=536;
	private $nDirectoryOffset_Offset=540;
	
	public function ReadHeader($File)
	{
		if(!$File)
		{
			SpewError("WTF?!");
			return FALSE;
		}
		
		//echo "ReadInt=".ReadInt($File,$this->nDemoProtocol_Offset)."<br>";
		$this->szFileStamp=ReadData($File,$this->szFileStamp_Offset,6);
		$this->nDemoProtocol=ReadInt($File,$this->nDemoProtocol_Offset);
		$this->nNetProtocolVersion=ReadInt($File,$this->nNetProtocolVersion_Offset);
		$this->szMapName=ReadData($File,$this->szMapName_Offset,260);
		$this->szDllDir=ReadData($File,$this->szDllDir_Offset,260);
		$this->mapCRC=ReadUint($File,$this->mapCRC_Offset);
		$this->nDirectoryOffset=ReadInt($File,$this->nDirectoryOffset_Offset);
		//$this->PrintHeaderInto();
		
		if
		(
			$this->szFileStamp===FALSE||
			$this->nDemoProtocol===FALSE||
			$this->nNetProtocolVersion===FALSE||
			$this->szMapName===FALSE||
			$this->szDllDir===FALSE||
			$this->mapCRC===FALSE||
			$this->nDirectoryOffset===FALSE
		)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function PrintHeaderInto()
	{
		echo "DemoHeaderInfo:<br>";
		echo "szFileStamp=".$this->szFileStamp."<br>";
		echo "nDemoProtocol=".$this->nDemoProtocol."<br>";
		echo "nNetProtocolVersion=".$this->nNetProtocolVersion."<br>";
		echo "szMapName=".$this->szMapName."<br>";
		echo "szDllDir=".$this->szDllDir."<br>";
		echo "mapCRC=".dechex($this->mapCRC)."<br>";
		echo "nDirectoryOffset=".$this->nDirectoryOffset."<br>";
	}
	
	
};

class demoentry_t
{
	public $nEntryType;
	public $szDescription;
	public $nFlags;
	public $nCDTrack;
	public $fTrackTime;
	public $nFrames;
	public $nOffset;
	public $nFileLength;
	
	private $nEntryType_Offset=0;
	private $szDescription_Offset=4;
	private $nFlags_Offset=68;
	private $nCDTrack_Offset=72;
	private $fTrackTime_Offset=76;
	private $nFrames_Offset=80;
	private $nOffset_Offset=84;
	private $nFileLength_Offset=88;

	public function ReadEntry($File,$EntryOffset)
	{
		if(!$File||!$EntryOffset)
		{
			SpewError("WTF?!");
			return FALSE;
		}
		
		$this->nEntryType=ReadInt($File,$EntryOffset+$this->nEntryType_Offset);
		$this->szDescription=ReadData($File,$EntryOffset+$this->szDescription_Offset,64);
		$this->nFlags=ReadInt($File,$EntryOffset+$this->nFlags_Offset);
		$this->nCDTrack=ReadInt($File,$EntryOffset+$this->nCDTrack_Offset);
		$this->fTrackTime=ReadFloat($File,$EntryOffset+$this->fTrackTime_Offset);
		$this->nFrames=ReadInt($File,$EntryOffset+$this->nFrames_Offset);
		$this->nOffset=ReadInt($File,$EntryOffset+$this->nOffset_Offset);
		$this->nFileLength=ReadInt($File,$EntryOffset+$this->nFileLength_Offset);
		
		
		if
		(
			$this->nEntryType===FALSE||
			$this->szDescription===FALSE||
			$this->nFlags===FALSE||
			$this->nCDTrack===FALSE||
			$this->fTrackTime===FALSE||
			$this->nFrames===FALSE||
			$this->nOffset===FALSE||
			$this->nFileLength===FALSE
		)
		{
			return FALSE;
		}
		return TRUE;

	}
	
	public function PrintEntryInfo()
	{
		echo "nEntryType=".$this->nEntryType."<br>";
		echo "szDescription=".$this->szDescription."<br>";
		echo "nFlags=".$this->nFlags."<br>";
		echo "nCDTrack=".$this->nCDTrack."<br>";
		echo "fTrackTime=".$this->fTrackTime."<br>";
		echo "nFrames=".$this->nFrames."<br>";
		echo "nOffset=".$this->nOffset."<br>";
		echo "nFileLength=".$this->nFileLength."<br>";
	}
	
}


class CHL1DemoInfo
{
	
	private $SizeofHeader=544;
	private $SizeofEntity=92;
	
	private $szFileName;
	private $FileSize;
	private $DemoFile;
	private $DemoHeader;
	private $NumDemoEntries;
	private $DemoEntries=array();
	private $bIsValid;
	
	function  __construct($DemoFileName)
	{
		$this->bIsValid=FALSE;
		if(!file_exists($DemoFileName))
		{
			SpewError("Demo file \"".$DemoFileName."\" not exists.");
			return;
		}
		$this->szFileName=$DemoFileName;
		$this->FileSize=filesize($this->szFileName);
		if($this->FileSize<=($this->SizeofHeader+$this->SizeofEntity))
		{
			/*
				В демке должен быть заголовок и как минимум одна энтитя. (Вообще-то там их как минимум 2);
			*/
			SpewError("Demo file \"".$this->szFileName."\" too small.");
			fclose($this->DemoFile);
			return;
		}
		$this->DemoFile=fopen($this->szFileName,"rb");
		if($this->DemoFile==FALSE)
		{
			SpewError("Unable to open \"".$this->szFileName."\" in \"rb\" mode.");
			fclose($this->DemoFile);
			return;
		}
		$this->DemoHeader=new demoheader_t();
		if($this->DemoHeader->ReadHeader($this->DemoFile)===FALSE)
		{
			SpewError("Failed to read header from \"".$this->szFileName."\.");
			$this->DemoHeader->PrintHeaderInto();
		}
		//$this->DemoHeader->PrintHeaderInto();
		
		if($this->DemoHeader->szFileStamp!="HLDEMO")
		{
			SpewError("Invalid szFileStamp in \"".$this->szFileName."\". Should be \"HLDEMO\" instead of \"".$this->DemoHeader->szFileStamp."\".");
			fclose($this->DemoFile);
			return;
		}
		if($this->DemoHeader->nNetProtocolVersion!=48)
		{
			SpewError("Invalid nNetProtocolVersion in \"".$this->szFileName."\". Should be \"48\" instead of \"".$this->DemoHeader->nNetProtocolVersion."\".");
			fclose($this->DemoFile);
			return;			
		}
		
		if($this->DemoHeader->nDemoProtocol!=5)
		{
			SpewError("Invalid nNetProtocolVersion in \"".$this->szFileName."\". Should be \"5\" instead of \"".$this->DemoHeader->nDemoProtocol."\".");
			fclose($this->DemoFile);
			return;			
		}
		
		if($this->DemoHeader->nDirectoryOffset>=$this->FileSize)
		{
			SpewError("nDirectoryOffset in \"".$this->szFileName."\" > FileSize. (\"".$this->DemoHeader->nDirectoryOffset."\" > \"".$this->FileSize."\".");
			fclose($this->DemoFile);
			return;			
		}
		$this->NumDemoEntries=ReadInt($this->DemoFile,$this->DemoHeader->nDirectoryOffset);
		//echo "Num entr=".$this->NumDemoEntries."<br>";
		if($this->NumDemoEntries==0)
		{
			SpewError("Demo file \"".$this->szFileName."\" has no entries.");
			fclose($this->DemoFile);
			return;
		}
		for($i=0;$i<$this->NumDemoEntries;$i++)
		{
			//array_push($this->DemoEntries, new demoentry_t());
			$this->DemoEntries[$i]=new demoentry_t();
			if($this->DemoEntries[$i]->ReadEntry($this->DemoFile,$this->DemoHeader->nDirectoryOffset+4+$this->SizeofEntity*$i)===FALSE)
			{
				SpewError("Failed to read DemoEntry($i) from \"".$this->szFileName."\.");
				$this->DemoEntries[$i]->PrintEntryInfo();
			}
			//$this->DemoEntries[$i]->PrintEntryInfo();
		}		
		fclose($this->DemoFile);
		$this->bIsValid=TRUE;
	}
	
	
	public function IsValid()
	{
		return $this->bIsValid;
	}
	
	public function SpewFullInfo()
	{
		if(!$this->IsValid())
		{
			echo "DemoFile is not valid<br>";
			return;
		}
		$this->DemoHeader->PrintHeaderInto();
		echo "<br>";
		foreach ($this->DemoEntries as &$Enrty) 
		{
			$Enrty->PrintEntryInfo();
			echo "<br>";
		}
	}
	
	public function GetGameTitle()
	{
		if(!$this->IsValid())
		{
			return FALSE;
		}
		return $this->DemoHeader->szDllDir;
	}
	
	public function GetMapName()
	{
		if(!$this->IsValid())
		{
			return FALSE;
		}
		return $this->DemoHeader->szMapName;
	}
	
	public function GetDemoTime()
	{
		if(!$this->IsValid())
		{
			return FALSE;
		}
		$time=0;
		foreach ($this->DemoEntries as &$Enrty) 
		{
			$time+=$Enrty->fTrackTime;
		}
		return $time;
	}
}
?>